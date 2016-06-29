<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/04/2016
 * Time: 4:41 PM
 */

namespace AlthingiAggregator\Lib\Consumer;

use AlthingiAggregator\Extractor\Exception;
use AlthingiAggregator\Lib\CacheableAwareInterface;
use DOMElement;
use Psr\Log\LoggerInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Psr\Log\LoggerAwareInterface;
use AlthingiAggregator\Lib\ClientAwareInterface;
use AlthingiAggregator\Lib\ConfigAwareInterface;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\ExtractionInterface;

class RestServerConsumer implements
    ConsumerInterface,
    ConfigAwareInterface,
    LoggerAwareInterface,
    ClientAwareInterface,
    CacheableAwareInterface
{
    /** @var  array */
    private $config;

    /** @var  \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Zend\Http\Client */
    private $client;

    /** @var  StorageInterface */
    private $cache;

    /**
     * Send $extract to REST server via HTTP.
     *
     * @param DOMElement $element
     * @param string $api
     * @param ExtractionInterface $extract
     * @return array
     */
    public function save(DOMElement $element, $api, ExtractionInterface $extract)
    {
        $host = $this->config['server']['host'];
        $entry = null;

        try {
            $entry = $extract->extract($element);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [$api]);
            return null;
        }

        try {
            //PUT request
            if ($extract instanceof IdentityInterface) {
                $uri = sprintf('%s/%s/%s', $host, $api, $extract->getIdentity());
                $cachedHash = $this->cache->getItem(md5($uri));
                $inputHash = $this->createHash($entry);

                if ($cachedHash === $inputHash) {
                    $this->logger->debug('Entry found in cache - Not sending to Server', [
                        sprintf('%s/%s/%s', $host, $api, $extract->getIdentity()),
                        $entry
                    ]);
                    return null;
                }

                $apiRequest = (new Request())
                    ->setMethod('post')
                    ->setHeaders((new Headers())->addHeaders([
                        'X-HTTP-Method-Override' => 'PUT'
                    ]))
                    ->setUri($uri)
                    ->setPost(new Parameters($entry));

                $apiResponse = $this->client->send($apiRequest);

                if (201 == $apiResponse->getStatusCode()) {
                    $this->cache->setItem(md5($uri), $inputHash);
                    $this->logger->debug('Storing entry in cache', [
                        sprintf('%s/%s/%s', $host, $api, $extract->getIdentity()),
                        $entry
                    ]);
                    $this->logger->debug($apiResponse->getStatusCode(), [
                        'PUT',
                        sprintf('%s/%s/%s', $host, $api, $extract->getIdentity()),
                        $entry
                    ]);
                } elseif (409 == $apiResponse->getStatusCode()) {
                    $patchRequest = (new Request())
                        ->setMethod('post')
                        ->setHeaders((new Headers())->addHeaders([
                            'X-HTTP-Method-Override' => 'PATCH'
                        ]))
                        ->setUri(sprintf('%s/%s/%s', $host, $api, $extract->getIdentity()))
                        ->setPost(new Parameters($entry));

                    $patchResponse = $this->client->send($patchRequest);

                    if (2 == (int) ($patchResponse->getStatusCode()/100)) {
                        $this->cache->setItem(md5($uri), $inputHash);
                        $this->logger->debug('Storing entry in cache', [
                            sprintf('%s/%s/%s', $host, $api, $extract->getIdentity()),
                            $entry
                        ]);
                        $this->logger->debug(
                            $patchResponse->getStatusCode(),
                            [
                                'PATCH',
                                sprintf('%s/%s/%s', $host, $api, $extract->getIdentity()),
                                $entry,
                                $patchResponse->getContent()
                            ]
                        );
                    } else {
                        $this->logger->warning(
                            $patchResponse->getStatusCode(),
                            [
                                'PATCH',
                                sprintf('%s/%s/%s', $host, $api, $extract->getIdentity()),
                                $entry,
                                $patchResponse->getContent()
                            ]
                        );
                    }
                } else {
                    $this->logger->warning(
                        $apiResponse->getStatusCode(),
                        [
                            'PATCH',
                            sprintf('%s/%s/%s', $host, $api, $extract->getIdentity()),
                            $entry,
                            $apiResponse->getContent()
                        ]
                    );
                }
            //POST request
            } else {
                $uri = sprintf('%s/%s', $host, $api);
                $cachedHash = $this->cache->getItem(md5($uri));
                $inputHash = $this->createHash($entry);

                $apiRequest = (new Request())
                    ->setMethod('post')
                    ->setUri(sprintf('%s/%s', $host, $api))
                    ->setPost(new Parameters($entry));


                if ($cachedHash === $inputHash) {
                    $this->logger->debug('Entry found in cache - Not sending to Server', [
                        sprintf('%s/%s', $host, $api),
                        $entry
                    ]);
                    return null;
                }

                $apiResponse = $this->client->send($apiRequest);

                switch ($apiResponse->getStatusCode()) {
                    case 201:
                        $this->logger->debug(
                            $apiResponse->getStatusCode(),
                            [
                                'POST',
                                sprintf('%s/%s', $host, $api),
                                $entry,
                                $apiResponse->getContent()
                            ]
                        );
                        break;
                    case 409:
                        if (!$apiResponse->getHeaders()->get('Location')) {
                            $this->logger->warning(
                                $apiResponse->getStatusCode(),
                                [
                                    'POST',
                                    sprintf('%s/%s', $host, $api),
                                    $entry,
                                    'No Location header'
                                ]
                            );
                            break;
                        }
                        $location = $apiResponse->getHeaders()->get('Location')->getFieldValue();
                        $uri = sprintf('%s%s', $host, $location);
                        $patchRequest = (new Request())
                            ->setMethod('post')
                            ->setHeaders((new Headers())->addHeaders([
                                'X-HTTP-Method-Override' => 'PATCH'
                            ]))
                            ->setUri($uri)
                            ->setPost(new Parameters($entry));

                        $patchResponse = $this->client->send($patchRequest);

                        if (2 == (int) ($patchResponse->getStatusCode()/100)) { //204
                            $this->cache->setItem(md5($uri), $inputHash);
                            $this->logger->debug('Storing entry in cache', [
                                sprintf('%s/%s', $host, $api),
                                $entry
                            ]);
                            $this->logger->debug(
                                $patchResponse->getStatusCode(),
                                [
                                    'PATCH',
                                    $uri,
                                    $entry,
                                    $patchResponse->getContent()
                                ]
                            );
                        } else {
                            $this->logger->warning(
                                $patchResponse->getStatusCode(),
                                [
                                    'PATCH',
                                    $uri,
                                    $entry,
                                    $patchResponse->getContent()
                                ]
                            );
                        }
                        break;
                    default:
                        $this->logger->warning(
                            $apiResponse->getStatusCode(),
                            [
                                'POST',
                                sprintf('%s/%s', $host, $api),
                                $entry,
                                $apiResponse->getContent()
                            ]
                        );
                        break;
                }
            }

        } catch (\Exception $e) {
            $this->logger->error($this->client->getUri() . ' -> ' . $e->getTraceAsString());
        }

        return $entry;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
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
     * @param array $entry
     * @return mixed
     */
    private function createHash(array $entry)
    {
        return md5(implode(',', $entry));
    }
}
