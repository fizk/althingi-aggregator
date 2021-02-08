<?php
namespace App\Consumer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use App\Extractor\ExtractionInterface;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Diactoros\{Request, Uri, Response, StreamFactory};
use App\Event\{ConsumerSuccessEvent, ConsumerErrorEvent};
use App\Lib\{
    IdentityInterface,
    CacheableAwareInterface,
    ClientAwareInterface,
    EventDispatcherAware,
    UriAwareInterface
};
use DOMElement;
use Exception;

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
    public function save(string $storageKey, ExtractionInterface $extract)
    {
        $tries = 3;

        do {
            try {
                if ($extract instanceof IdentityInterface) {
                    return $this->doIdentityRequest($storageKey, $extract->getIdentity(), $extract->extract());
                } else {
                    return $this->doUniqueRequest($storageKey, $extract->extract());
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
        $postRequest = $this->getRequest('POST', $uri, $params);
        if ($this->isValidInCache($uri, $params)) {
            $this->getEventDispatcher()->dispatch(new ConsumerSuccessEvent(
                $postRequest,
                new Response('php://memory', 304)
            ));
            return;
        }

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
                        new Exception($postResponse->getBody()->__toString())
                    ));
                break;
        }
    }

    private function doPutRequest(Uri $uri, array $params): void
    {
        $putRequest = $this->getRequest('PUT', $uri, $params);
        if ($this->isValidInCache($uri, $params)) {
            $this->getEventDispatcher()->dispatch(new ConsumerSuccessEvent(
                $putRequest,
                new Response('php://memory', 304)
            ));
            return;
        }

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
                        new Exception($putResponse->getBody()->__toString())
                    ));
                break;
        }
    }

    private function doPatchRequest(Uri $uri, array $params): void
    {
        $patchRequest = $this->getRequest('PATCH', $uri, $params);
        if ($this->isValidInCache($uri, $params)) {
            $this->getEventDispatcher()->dispatch(new ConsumerSuccessEvent(
                $patchRequest,
                new Response('php://memory', 304)
            ));
            return;
        }

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
                        new Exception($patchResponse->getBody()->__toString())
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
        // $lines = [];
        // foreach ($param as $key => $value) {
        //     $lines[] = "{$key}=" . urlencode($value);
        // }
        $request = (new Request($uri, 'POST', (new StreamFactory())->createStream(http_build_query($param)), [
            'X-HTTP-Method-Override' => $verb,
            'X-Transaction-Id' => sha1(uniqid(rand(), true)),
            'Connection' => 'Keep-Alive',
            'Keep-Alive' => 'timeout=5, max=1000',
            'Content-Type' => 'application/x-www-form-urlencoded'
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
