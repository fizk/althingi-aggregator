<?php
namespace AlthingiAggregator\Lib\Provider;

use AlthingiAggregator\Lib\CacheableAwareInterface;
use AlthingiAggregator\Lib\ClientAwareInterface;
use AlthingiAggregator\Lib\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;

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
    public function get($url, callable $cb = null)
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
                $this->logger->info(0, ['Can\'t connect to provider, ' . ($tries - 1) . ' tries left']);
                sleep(2);
                $tries--;

                if ($tries === 0) {
                    $this->logger->error(0, ['Service is unavailable', $e->getMessage()]);
                    throw $e;
                }
            }
        } while ($tries > 0);

        if ($cb) {
            $newDom = $cb($content);
            $this->cache->setItem($key, $content);
            return $newDom;
        } else {
            $dom = @new \DOMDocument();
            if (@$dom->loadXML($content)) {
                $this->cache->setItem($key, $content);
                return $dom;
            } else {
                throw new \Exception(print_r(error_get_last(), true));
            }
        }
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

    private function cacheRequest($url)
    {
        $this->logger->info(0, ['PROVIDER_CACHE', $url]);
        return $this->cache->getItem(md5($url));
    }

    private function httpRequest($url)
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

        if ($status === 200) {
            $this->logger->info(0, ['HTTP', $url]);
            return $response->getBody();
        } else {
            $this->logger->error($status, ['HTTP_ERROR', $response->getReasonPhrase(), $request->getUriString()]);
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
}
