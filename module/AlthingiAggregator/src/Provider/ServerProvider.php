<?php
namespace AlthingiAggregator\Provider;

use AlthingiAggregator\Lib\CacheableAwareInterface;
use AlthingiAggregator\Lib\ClientAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;
use DOMDocument;

class ServerProvider implements
    ProviderInterface,
    ClientAwareInterface,
    LoggerAwareInterface,
    CacheableAwareInterface
{
    /** @var  Client */
    private $client;

    /** @var  \Psr\Log\LoggerInterface */
    private $logger;

    /** @var  StorageInterface */
    private $cache;

    /**
     * @param string $url
     * @param callable $cb
     * @return \DOMDocument
     * @throws \Exception
     */
    public function get(string $url, callable $cb = null): DOMDocument
    {
        $tries = 3;
        $content = '';
        $key = md5($url);

        do {
            try {
                $content = $this->cache->hasItem($key)
                    ? $this->cacheRequest($url)
                    : $this->httpRequest($url);

                $tries = 0;
            } catch (\Exception $e) {
                $this->logger->info(0, ['HTTP', $url, [
                    'message' => 'Can\'t connect to provider, ' . ($tries - 1) . ' tries left'
                ]]);
                sleep(2);
                $tries--;

                if ($tries === 0) {
                    $this->logger->error(0, ['HTTP', $url, [
                        'message' => 'Service is unavailable',
                        'exception' => $e->getMessage()
                    ]]);
                    throw $e;
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
                if (in_array($entry, ['&lt;', '&gt;', '&apos;', '&amp;'])) {
                    return '';
                }
                return html_entity_decode($entry);
            }, $content);
            $dom = @new DOMDocument();
            if (@$dom->loadXML($content)) {
                $this->cache->setItem($key, $content);
                return $dom;
            } else {
                throw new \Exception(json_encode(array_merge(error_get_last(), ['url' => $url])));
            }
        }
    }

    private function cacheRequest(string $url): string
    {
        $this->logger->info(0, ['PROVIDER_CACHE', $url, [], []]);
        return $this->cache->getItem(md5($url));
    }

    /**
     * @param $url
     * @return string
     * @throws \Exception
     */
    private function httpRequest(string $url): string
    {
        $request = new Request();
        $request->setMethod('get')
            ->setUri($url)
            ->setHeaders((new Headers())->addHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) '.
                    'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36'
            ]));

        try {
            $response = $this->client->send($request);
        } catch (\Exception $e) {
            sleep(10);
            $response = $this->client->send($request);
        }

        $status = $response->getStatusCode();

        if ($status === 200) { //success retrieving document
            $this->logger->info(0, ['HTTP', $url, $response->getHeaders()->toArray()]);
            return $response->getBody();
        } elseif ($status === 403) { // access denied, try again
            throw new \Exception($response->getReasonPhrase() . ' | ' . $request->getUriString(), $status);
        } else { //other errors
            $this->logger->error($status, ['HTTP', $request->getUriString(), [
                'message' => $response->getReasonPhrase()
            ], ]);
            return $response->getBody();
        }
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @param StorageInterface $cache
     * @return $this
     */
    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }
}
