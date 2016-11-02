<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 5/06/2016
 * Time: 10:42 AM
 */

namespace AlthingiAggregator\Lib\Consumer;

use AlthingiAggregator\Lib\IdentityInterface;
use DOMElement;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\Http\Client;
use AlthingiAggregator\Extractor\ExtractionInterface;
use AlthingiAggregator\Lib\CacheableAwareInterface;
use AlthingiAggregator\Lib\ClientAwareInterface;
use AlthingiAggregator\Lib\UriAwareInterface;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Zend\Uri\Http;

class HttpConsumer implements
    ConsumerInterface,
    LoggerAwareInterface,
    ClientAwareInterface,
    CacheableAwareInterface,
    UriAwareInterface
{

    /** @var  Http */
    private $uri;

    /** @var  \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Zend\Http\Client */
    private $client;

    /** @var  StorageInterface */
    private $cache;

    /**
     * Save $extract to storage/consumer.
     *
     * @param DOMElement $element
     * @param string $storageKey
     * @param ExtractionInterface $extract
     * @return mixed
     */
    public function save(DOMElement $element, $storageKey, ExtractionInterface $extract)
    {
        $params = $extract->extract($element);

        return ($extract instanceof IdentityInterface)
            ? $this->doIdentityRequest($storageKey, $extract->getIdentity(), $params)
            : $this->doUniqueRequest($storageKey, $params);
    }

    private function doIdentityRequest($storageKey, $identity, $params)
    {
        $uri = new Http($this->uri->toString());
        $uri->setPath(sprintf('/%s/%s', $storageKey, $identity));
        if (getenv('XDEBUG_START')) {
            $uri->setQuery(['XDEBUG_SESSION_START' => getenv('XDEBUG_START')]);
        }

        if ($this->isValidInCache($uri, $params)) {
            $this->logger->notice('- ', [$uri->toString(), $params]);
        } else {
            $this->doPutRequest($uri, $params);
        }

        return $params;
    }

    private function doUniqueRequest($storageKey, $params)
    {
        $uri = new Http($this->uri->toString());
        $uri->setPath(sprintf('/%s', $storageKey));
        if (getenv('XDEBUG_START')) {
            $uri->setQuery(['XDEBUG_SESSION_START' => getenv('XDEBUG_START')]);
        }

        if ($this->isValidInCache($uri, $params)) {
            $this->logger->notice('- ', [$uri->toString(), $params]);
        } else {
            $this->doPostRequest($uri, $params);
        }

        return $params;
    }

    private function doPostRequest(Http $uri, array $params)
    {
        $postRequest = $this->getRequest('POST', $uri, $params);
        $postResponse = $this->client->send($postRequest);

        switch ($postResponse->getStatusCode()) {
            case 201:
                $this->storeInCache($uri, $params);
                $this->logger->info(
                    $postResponse->getStatusCode(),
                    ['POST', $uri->toString(), $params, $postResponse->getContent()]
                );
                break;
            case 400:
                $this->logger->error(
                    $postResponse->getStatusCode(),
                    ['POST', $uri->toString(), $params, $postResponse->getContent()]
                );
                break;
            case 409:
                if ($postResponse->getHeaders()->get('Location')) {
                    $this->doPatchRequest(
                        $uri->setPath($postResponse->getHeaders()->get('Location')->getFieldValue()),
                        $params
                    );
                } else {
                    $this->logger->warning(
                        'Can\'t PATCH, no Location-Header',
                        ['POST', $uri->toString(), $params, $postResponse->getContent()]
                    );
                }
                break;
            default:
                $this->logger->critical(
                    $postResponse->getStatusCode(),
                    ['POST', $uri->toString(), $params, $postResponse->getContent()]
                );
                break;
        }
    }

    private function doPutRequest(Http $uri, array $params)
    {
        $putRequest = $this->getRequest('PUT', $uri, $params);
        $putResponse = $this->client->send($putRequest);

        switch ($putResponse->getStatusCode()) {
            case 201:
                $this->storeInCache($uri, $params);
                $this->logger->info(
                    201,
                    ['PUT', $uri->toString(), $params, $putResponse->getContent()]
                );
                break;
            case 418:
                $this->logger->notice(
                    418,
                    ['PUT', $uri->toString(), $params, $putResponse->getContent()]
                );
                break;
            case 400:
                $this->logger->error(
                    $putResponse->getStatusCode(),
                    ['PUT', $uri->toString(), $params, $putResponse->getContent()]
                );
                break;
            case 409:
                $this->doPatchRequest($uri, $params);
                break;
            default:
                $this->logger->critical(
                    $putResponse->getStatusCode(),
                    ['PUT', $uri->toString(), $params, $putResponse->getContent()]
                );
                break;
        }
    }

    private function doPatchRequest(Http $uri, array $params)
    {
        $patchRequest = $this->getRequest('PATCH', $uri, $params);
        $patchResponse = $this->client->send($patchRequest);

        switch ($patchResponse->getStatusCode()) {
            case 205:
                $this->cache->setItem(
                    self::createStorageKey($uri),
                    self::createStorageValue($params)
                );
                $this->logger->info(
                    205,
                    ['PATCH', $uri->toString(), $params, $patchResponse->getContent()]
                );
                break;
            case 404:
                $this->logger->error(
                    404,
                    ['PATCH', $uri->toString(), $params, $patchResponse->getContent()]
                );
                break;
            default:
                $this->logger->critical(
                    $patchResponse->getStatusCode(),
                    ['PATCH', $uri->toString(), $params, $patchResponse->getContent()]
                );
                break;
        }
    }

    private function storeInCache(Http $uri, $param)
    {
        $this->cache->setItem(
            self::createStorageKey($uri),
            self::createStorageValue($param)
        );
    }

    private function isValidInCache(Http $uri, $param)
    {
        $cacheValue = $this->cache->getItem(self::createStorageKey($uri));

        return $cacheValue == self::createStorageValue($param);
    }

    /**
     * @param string $verb
     * @param Http $uri
     * @param array $param
     * @return Request
     */
    private function getRequest($verb, Http $uri, array $param)
    {
        return (new Request())
            ->setMethod('post')
            ->setHeaders((new Headers())->addHeaders([
                'X-HTTP-Method-Override' => $verb
            ]))
            ->setUri($uri)
            ->setPost(new Parameters($param));
    }

    /**
     * @param Http $uri
     * @return $this
     */
    public function setUri(Http $uri)
    {
        $this->uri =  $uri;
        return $this;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
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

    /**
     * @param StorageInterface $cache
     * @return $this
     */
    public function setCache(StorageInterface $cache)
    {
        $this->cache = clone $cache;
        $this->cache->setOptions(array_merge(
            $this->cache->getOptions()->toArray(),
            ['namespace' => 'consumer', 'cache_dir' => './data/cache/consumer', 'ttl' => (60*60*24*365)])
        );
        return $this;
    }

    /**
     * Create a key to store value under in cache.
     *
     * @param Http $uri
     * @return string
     */
    public static function createStorageKey(Http $uri)
    {
        return md5($uri->toString());
    }

    /**
     * Convert $params into a hash to store in cache.
     *
     * @param array $entry
     * @return mixed
     */
    public static function createStorageValue(array $entry)
    {
        return md5(json_encode($entry));
    }
}
