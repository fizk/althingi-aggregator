<?php

namespace AlthingiAggregator\Lib\MediaClient;

use AlthingiAggregator\Extractor\Exception;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Zend\Uri\Http;

class ThumborClient implements MediaClientAdapter
{
    /** @var  string */
    private $uri;

    /** @var  \Zend\Http\Client */
    private $client;

    /**
     * @param string $file
     * @param string $slug
     * @param string $contentType
     * @return int
     * @throws \Exception
     */
    public function save($file, $slug, $contentType)
    {
        $originalImageRequest = (new Request())
            ->setMethod('get')
            ->setHeaders((new Headers())->addHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Host' => 'www.althingi.is'
            ]))
            ->setUri(new Http($file));


        $originalImageResponse = $this->client->send($originalImageRequest);
        $uri = new Http($this->getUri());
        /** @var  $thumborUploadRequest \Zend\Http\Request */
        $thumborUploadRequest = (new Request())
            ->setMethod('POST')
            ->setHeaders((new Headers())->addHeaders([
                'Content-Type' => $contentType,
                'Slug' => $slug
            ]))
            ->setUri($uri)
            ->setContent($originalImageResponse->getBody())
        ;

        $thumborUploadResponse = $this->client
            ->send($thumborUploadRequest);

        if ($thumborUploadResponse->getStatusCode() !== 201) {
            throw new \Exception(
                "Thumbor returned with {$uri->toString()} {$thumborUploadResponse->getStatusCode()}",
                $thumborUploadResponse->getStatusCode()
            );
        } else {
            return (int) $thumborUploadResponse
                ->getHeaders()
                ->get('Content-Length')
                ->getFieldValue();
        }
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     * @return ThumborClient
     */
    public function setUri(string $uri): ThumborClient
    {
        $this->uri = $uri;
        return $this;
    }
}
