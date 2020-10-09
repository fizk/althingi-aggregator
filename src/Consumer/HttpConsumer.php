<?php
namespace App\Consumer;

use App\Event\ConsumerErrorEvent;
use App\Event\ConsumerSuccessEvent;
use Psr\Http\Client\ClientInterface;
use App\Lib\IdentityInterface;
use App\Extractor\ExtractionInterface;
use App\Lib\CacheableAwareInterface;
use App\Lib\ClientAwareInterface;
use App\Lib\EventDispatcherAware;
use App\Lib\UriAwareInterface;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\Uri;
use DOMElement;
use Exception;
use Laminas\Diactoros\StreamFactory;
use Psr\EventDispatcher\EventDispatcherInterface;

class HttpConsumer implements
    ConsumerInterface,
    ClientAwareInterface,
    CacheableAwareInterface,
    UriAwareInterface,
    EventDispatcherAware
{
    private Uri $uri;
    private ClientInterface $client;
    private StorageInterface $cache;
    private ?EventDispatcherInterface $eventDispatch = null;

    /**
     * Save $extract to storage/consumer.
     *
     * @param DOMElement $element
     * @param string $storageKey
     * @param ExtractionInterface $extract
     * @return mixed
     * @throws
     */
    public function save(DOMElement $element, string $storageKey, ExtractionInterface $extract)
    {
        $params = $extract->extract($element);
        $tries = 3;

        do {
            try {
                if ($extract instanceof IdentityInterface) {
                    return $this->doIdentityRequest($storageKey, $extract->getIdentity(), $params);
                } else {
                    return $this->doUniqueRequest($storageKey, $params);
                }
            } catch (\Exception $e) {
                sleep(2);
                $tries--;

                if ($tries === 0) {
                    throw $e;
                }
            }
        } while ($tries > 0);

        return [];
    }

    private function doIdentityRequest($storageKey, string $identity, $params): array
    {
        $this->doPutRequest(
            $this->uri->withPath(sprintf('/%s/%s', $storageKey, $identity)),
            $params
        );

        return $params;
    }

    private function doUniqueRequest(string $storageKey, array $params): array
    {
        $this->doPostRequest(
            $this->uri->withPath(sprintf('/%s', $storageKey)),
            $params
        );

        return $params;
    }

    private function doPostRequest(Uri $uri, array $params): void
    {
        if ($this->isValidInCache($uri, $params)) {
            return;
        }

        $postRequest = $this->getRequest('POST', $uri, $params);
        $postResponse = $this->client->sendRequest($postRequest);

        switch ($postResponse->getStatusCode()) {
            case 201:
            case 202:
            case 204:
            case 205:
                $this->storeInCache($uri, $params);
                $this->getEventDispatcher()
                    ->dispatch(new ConsumerSuccessEvent($postRequest, $postResponse));
                break;
            case 409:
                $locationArray = $postResponse->getHeader('Location');
                if (count($locationArray) > 0) {
                    $this->doPatchRequest($uri->withPath($locationArray[0]), $params);
                } else {
                    $this->getEventDispatcher()
                        ->dispatch(new ConsumerErrorEvent(
                            $postRequest,
                            $postResponse,
                            new Exception('Can\'t PATCH, no Location-Header')
                        ));
                }
                break;
            default:
                $this->getEventDispatcher()
                    ->dispatch(new ConsumerErrorEvent(
                        $postRequest,
                        $postResponse,
                        new Exception("Unsupported status code from consumer {$postResponse->getStatusCode()}")
                    ));
                break;
        }
    }

    private function doPutRequest(Uri $uri, array $params): void
    {
        if ($this->isValidInCache($uri, $params)) {
            return;
        }

        $putRequest = $this->getRequest('PUT', $uri, $params);
        $putResponse = $this->client->sendRequest($putRequest);

        switch ($putResponse->getStatusCode()) {
            case 201:
            case 202:
            case 204:
            case 205:
                $this->storeInCache($uri, $params);
                $this->getEventDispatcher()
                    ->dispatch(new ConsumerSuccessEvent($putRequest, $putResponse));
                break;
            case 409:
                $this->doPatchRequest($uri, $params);
                break;
            default:
                $this->getEventDispatcher()
                    ->dispatch(new ConsumerErrorEvent(
                        $putRequest,
                        $putResponse,
                        new Exception("Unsupported status code from consumer {$putResponse->getStatusCode()}")
                    ));
                break;
        }
    }

    private function doPatchRequest(Uri $uri, array $params): void
    {
        if ($this->isValidInCache($uri, $params)) {
            return;
        }

        $patchRequest = $this->getRequest('PATCH', $uri, $params);
        $patchResponse = $this->client->sendRequest($patchRequest);

        switch ($patchResponse->getStatusCode()) {
            case 201:
            case 202:
            case 204:
            case 205:
                $this->storeInCache($uri, $params);
                $this->getEventDispatcher()
                    ->dispatch(new ConsumerSuccessEvent($patchRequest, $patchResponse));
                break;
            default:
                $this->getEventDispatcher()
                    ->dispatch(new ConsumerErrorEvent(
                        $patchRequest,
                        $patchResponse,
                        new Exception("Unsupported status code from consumer {$patchResponse->getStatusCode()}")
                    ));
                break;
        }
    }

    private function storeInCache(Uri $uri, array $param): void
    {
        $this->cache->setItem(
            self::createStorageKey($uri),
            self::createStorageValue($param)
        );
    }

    private function isValidInCache(Uri $uri, array $param): bool
    {
        $storageKey = self::createStorageKey($uri);
        $cacheValue = $this->cache->getItem($storageKey);
        $createdValue = self::createStorageValue($param);

        return $cacheValue == $createdValue;
    }

    private function getRequest($verb, Uri $uri, array $param): Request
    {
        $lines = [];
        foreach ($param as $key => $value) {
            $lines[] = "{$key}=" . urlencode($value);
        }
        $request = (new Request($uri, 'POST', (new StreamFactory())->createStream(implode("\r\n", $lines)), [
            'X-HTTP-Method-Override' => $verb,
            'X-Transaction-Id' => sha1(uniqid(rand(), true)),
            'Connection' => 'Keep-Alive',
            'Keep-Alive' => 'timeout=5, max=1000',
        ]));
        $request->getBody()->rewind();

        return $request;
    }

    public function setUri(Uri $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function setHttpClient(ClientInterface $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function setCache(StorageInterface $cache): self
    {
        $this->cache = $cache;
        return $this;
    }

    public static function createStorageKey(Uri $uri): string
    {
        return md5($uri->__toString());
    }

    public static function createStorageValue(array $entry): string
    {
        return md5(json_encode($entry));
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatch ?: new class implements EventDispatcherInterface
        {
            public function dispatch(object $event)
            {
            }
        };
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatch): self
    {
        $this->eventDispatch = $eventDispatch;
        return $this;
    }
}
