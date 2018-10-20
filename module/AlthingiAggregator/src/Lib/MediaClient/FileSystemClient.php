<?php
namespace AlthingiAggregator\Lib\MediaClient;

use \Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Uri\Http;

class FileSystemClient implements MediaClientAdapter
{
    /** @var  string */
    private $base;

    /** @var  \Zend\Http\Client */
    private $client;

    /**
     * @param string $file
     * @param string $slug
     * @param string $contentType
     * @return int
     */
    public function save($file, $slug, $contentType)
    {
        $request = (new Request())
            ->setMethod('get')
            ->setHeaders((new Headers())->addHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; '.
                    'Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Host' => 'www.althingi.is'
            ]))
            ->setUri(new Http($file));

        try {
            $response = $this->client->send($request);
            return file_put_contents("{$this->base}/{$slug}", $response->getContent());
        } catch (\Exception $e) {
            return 0;
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
    public function getBase(): string
    {
        return $this->base;
    }

    /**
     * @param string $base
     * @return $this
     */
    public function setBase(string $base)
    {
        $this->base = $base;
        return $this;
    }
}
