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

    /** @var array */
    private $options = [
        'save' => false
    ];

    /**
     * ServerProvider constructor.
     *
     * Options array can have one key; 'save' => true
     * will save the XML document to disk.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @param string $url
     * @return \DOMDocument
     * @throws \Exception
     */
    public function get($url)
    {
        $content = '';
        $cacheHit = false;

        if (is_file($this->generatePath($url))) {
            $content = file_get_contents($this->generatePath($url));
            $cacheHit = true;
            $this->logger->debug("Cache hit - " . $url);
        } else {
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
                $content = $response->getBody();
            } else {
                $this->logger->warning($response->getReasonPhrase());
                throw new \Exception($response->getReasonPhrase());
            }
            $this->logger->debug("None cache hit - fetching file: " . $url);
        }

        if ($this->options['save'] && !$cacheHit) {
            $this->saveFile($url, $content);
        }

        $dom = @new \DOMDocument();
        if ($dom->loadXML($content)) {
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

    /**
     * Set options.
     *
     * Options array can have one key; 'save' => true
     * will save the XML document to disk.
     *
     * @param $options
     */
    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    private function saveFile($url, $content)
    {
        $path = str_replace('http://', '', $url);

        $pathArray = explode('/', $path);

        $fileName = array_pop($pathArray);
        $filePath = './'. implode('/', $pathArray);

        @mkdir($filePath, 0777, true);

        file_put_contents($filePath . '/' . $fileName . '.xml', $content);
    }

    private function generatePath($url)
    {
        $path = str_replace('http://', '', $url);

        $pathArray = explode('/', $path);

        $fileName = array_pop($pathArray);
        $filePath = './'. implode('/', $pathArray);

        return $filePath . '/' . $fileName . '.xml';
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
        $this->cache = $cache;
    }
}
