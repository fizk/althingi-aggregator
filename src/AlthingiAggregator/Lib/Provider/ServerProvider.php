<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/04/2016
 * Time: 5:27 PM
 */

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
     * @return \DOMDocument
     * @throws \Exception
     */
    public function get($url)
    {
        $key = md5($url);
        $content = $this->cache->hasItem($key)
            ? $this->cacheRequest($url)
            : $this->httpRequest($url);

        $dom = @new \DOMDocument();
        if ($dom->loadXML($content)) {
            $this->cache->setItem($key, $content);
            return $dom;
        } else {
            $this->logger->error(print_r(error_get_last(), true));
            throw new \Exception(print_r(error_get_last(), true));
        }
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    private function cacheRequest($url)
    {
        $this->logger->debug('CACHE Request: ' . $url);
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
        $response = $this->client->send($request);

        $status = $response->getStatusCode();

        if ($status === 200) {
            $this->logger->debug('HTTP Request: ' . $url);
            return $response->getBody();
        } else {
            $this->logger->error($response->getReasonPhrase(), [$status, $request->getUriString()]);
//            throw new \Exception($response->getReasonPhrase());
        }
    }

    /**
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    public function setCache(StorageInterface $cache)
    {
        $this->cache = clone $cache;
        $this->cache->setOptions(array_merge(
            $this->cache->getOptions()->toArray(),
            ['namespace' => 'provider', 'cache_dir' => './data/cache/provider', 'ttl' => (60*60*24*2)])
        );
        return $this;
    }
}
