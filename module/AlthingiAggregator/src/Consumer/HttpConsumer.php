<?php
namespace AlthingiAggregator\Consumer;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\ExtractionInterface;
use AlthingiAggregator\Lib\CacheableAwareInterface;
use AlthingiAggregator\Lib\ClientAwareInterface;
use AlthingiAggregator\Lib\UriAwareInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Zend\Uri\Http;
use DOMElement;
use Zend\Uri\Uri;

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
     * @throws
     */
    public function save(DOMElement $element, $storageKey, ExtractionInterface $extract)
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
                $this->logger->info(0, ['CONSUMER', $storageKey, [
                    'message' => 'Can\'t connect to consumer, ' . ($tries - 1) . ' tries left'
                ]]);
                sleep(2);
                $tries--;

                if ($tries === 0) {
                    throw $e;
                }
            }
        } while ($tries > 0);

        return true;
    }

    private function doIdentityRequest($storageKey, $identity, $params)
    {
        $uri = new Http($this->uri->toString());
        $uri->setPath(sprintf('/%s/%s', $storageKey, $identity));

        $this->doPutRequest($uri, $params);

        return $params;
    }

    private function doUniqueRequest($storageKey, $params)
    {
        $uri = new Http($this->uri->toString());
        $uri->setPath(sprintf('/%s', $storageKey));

        $this->doPostRequest($uri, $params);

        return $params;
    }

    private function doPostRequest(Http $uri, array $params)
    {
        if ($this->isValidInCache($uri, $params)) {
            $this->logger->info(0, ['CONSUMER_CACHE', $uri->toString(), $params]);
            return true;
        }

        $postRequest = $this->getRequest('POST', $uri, $params);
        $postResponse = $this->client->send($postRequest);

        switch ($postResponse->getStatusCode()) {
            case 201:
            case 202:
            case 204:
            case 205:
                $this->storeInCache($uri, $params);
                $this->logger->info(
                    $postResponse->getStatusCode(),
                    ['POST', $uri->toString(), $params, $postResponse->getContent()]
                );
                break;
            case 409:
                if ($postResponse->getHeaders()->get('Location')) {
                    $this->logger->error(
                        $postResponse->getStatusCode(),
                        ['POST', $uri->toString(), [
                            'message' => 'Going to try PATCH', 'params' => $params
                        ], $postResponse->getContent()]
                    );
                    $this->doPatchRequest(
                        $uri->setPath($postResponse->getHeaders()->get('Location')->getFieldValue()),
                        $params
                    );
                } else {
                    $this->logger->error(
                        0,
                        ['POST', $uri->toString(), [
                            'message' => 'Can\'t PATCH, no Location-Header', 'params' => $params
                        ], $postResponse->getContent()]
                    );
                }
                break;
            default:
                $this->logger->error(
                    $postResponse->getStatusCode(),
                    ['POST', $uri->toString(), $params, $postResponse->getContent()]
                );
                break;
        }
    }

    private function doPutRequest(Http $uri, array $params)
    {
        if ($this->isValidInCache($uri, $params)) {
            $this->logger->info(0, ['CONSUMER_CACHE', $uri->toString(), $params]);
            return true;
        }

        $putRequest = $this->getRequest('PUT', $uri, $params);
        $putResponse = $this->client->send($putRequest);

        switch ($putResponse->getStatusCode()) {
            case 201:
            case 202:
            case 204:
            case 205:
                $this->storeInCache($uri, $params);
                $this->logger->info(
                    $putResponse->getStatusCode(),
                    ['PUT', $uri->toString(), $params, $putResponse->getContent()]
                );
                break;
            case 409:
                $this->logger->error(
                    $putResponse->getStatusCode(),
                    ['PUT', $uri->toString(), [
                        'message' => 'Going to try PATCH',
                        'params' => $params
                    ], $putResponse->getContent()]
                );
                $this->doPatchRequest($uri, $params);
                break;
            default:
                $this->logger->error(
                    $putResponse->getStatusCode(),
                    ['PUT', $uri->toString(), $params, $putResponse->getContent()]
                );
                break;
        }
    }

    private function doPatchRequest(Uri $uri, array $params)
    {
        if ($this->isValidInCache($uri, $params)) {
            $this->logger->info(0, ['CONSUMER_CACHE', $uri->toString(), $params]);
            return true;
        }

        $patchRequest = $this->getRequest('PATCH', $uri, $params);
        $patchResponse = $this->client->send($patchRequest);

        switch ($patchResponse->getStatusCode()) {
            case 201:
            case 202:
            case 204:
            case 205:
                $this->storeInCache($uri, $params);
                $this->logger->info(
                    $patchResponse->getStatusCode(),
                    ['PATCH', $uri->toString(), $params, $patchResponse->getContent()]
                );
                break;
            default:
                $this->logger->error(
                    $patchResponse->getStatusCode(),
                    ['PATCH', $uri->toString(), $params, $patchResponse->getContent()]
                );
                break;
        }
    }

    private function storeInCache(Uri $uri, $param)
    {
        $this->cache->setItem(
            self::createStorageKey($uri),
            self::createStorageValue($param)
        );
    }

    private function isValidInCache(Uri $uri, $param)
    {
        $storageKey = self::createStorageKey($uri);
        $cacheValue = $this->cache->getItem($storageKey);
        $createdValue = self::createStorageValue($param);

        return $cacheValue == $createdValue;
    }

    /**
     * @param string $verb
     * @param Http $uri
     * @param array $param
     * @return Request
     */
    private function getRequest($verb, Uri $uri, array $param)
    {
        return (new Request())
            ->setMethod('post')
            ->setHeaders((new Headers())->addHeaders([
                'X-HTTP-Method-Override' => $verb,
                'Keep-Alive' => 'timeout=5, max=1000',
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
        $this->uri = $uri;
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
        $this->cache = $cache;
        return $this;
    }

    /**
     * Create a key to store value under in cache.
     *
     * @param Uri $uri
     * @return string
     */
    public static function createStorageKey(Uri $uri)
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
