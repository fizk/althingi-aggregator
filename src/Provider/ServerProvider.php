<?php
namespace App\Provider;

use App\Event\{ProviderErrorEvent, ProviderSuccessEvent};
use Psr\Http\Client\{ClientInterface, ClientExceptionInterface};
use Psr\EventDispatcher\EventDispatcherInterface;
use Laminas\Diactoros\{Request, Response, Uri};
use Laminas\Cache\Storage\StorageInterface;
use App\Lib\{CacheableAwareInterface, ClientAwareInterface, EventDispatcherAware};
use DOMDocument;
use ErrorException;
use UnexpectedValueException;

class ServerProvider implements
    ProviderInterface,
    ClientAwareInterface,
    CacheableAwareInterface,
    EventDispatcherAware
{
    private ClientInterface $client;
    private StorageInterface $cache;
    private ?EventDispatcherInterface $eventDispatch = null;

    /**
     * @throws ErrorException|UnexpectedValueException
     */
    public function get(string $url, callable $cb = null): DOMDocument
    {
        $tries = 3;
        $content = '';
        $key = md5($url);

        do {
            try {
                $content = $this->cacheRequest($key, $url)
                    ?: $this->httpRequest($url);
                $tries = 0;
            } catch (ErrorException $error) {
                sleep(2);
                $tries--;

                if ($tries === 0) {
                    throw $error;
                }
            }
        } while ($tries > 0);

        if ($cb) {
            $newDom = $cb($content);
            $this->cache->setItem($key, $content);
            return $newDom;
        } else {
            $content = preg_replace_callback('/\&.*?;/im', function ($item) {
                $entry = (count($item) > 0) ? $item[0] : '';
                if (in_array($entry, ['&quot;', '&amp;', '&apos;', '&lt;', '&gt;'])) {
                    return $entry;
                }
                return html_entity_decode($entry);
            }, $content);

            $dom = @new DOMDocument();
            if (@$dom->loadXML($content)) {
                $this->cache->setItem($key, $content);
                return $dom;
            } else {
                throw new UnexpectedValueException(json_encode(array_merge(error_get_last(), ['url' => $url])));
            }
        }
    }

    /**
     * @throws ErrorException
     */
    private function httpRequest(string $url): string
    {
        $request = (new Request())
            ->withMethod('GET')
            ->withUri(new Uri($url))
            ->withHeader('User-Agent', 'unknown agent')
            ->withAddedHeader('Keep-Alive', 'timeout=5, max=1000')
            ->withAddedHeader('Connection', 'Keep-Alive');

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            sleep(10);
            $response = $this->client->sendRequest($request);
        }

        if ($response->getStatusCode() > 399) {
            $exception = new ErrorException(
                "Provider can't access {$request->getUri()->__toString()}, {$request->getUri()->__toString()}"
            );
            $this->getEventDispatcher()->dispatch(new ProviderErrorEvent($request, $response, $exception));
            throw $exception;
        }

        $this->getEventDispatcher()
            ->dispatch(new ProviderSuccessEvent($request, $response));
        return $response->getBody()->__toString();
    }

    private function cacheRequest(string $key, string $url): ?string
    {
        if ($this->cache->hasItem($key)) {
            $this->getEventDispatcher()->dispatch(new ProviderSuccessEvent(
                new Request($url, 'GET'),
                new Response('php://memory', 304)
            ));
            return $this->cache->getItem($key);
        }

        return null;
    }

    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    public function setHttpClient(ClientInterface $client): self
    {
        $this->client = $client;
        return $this;
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
