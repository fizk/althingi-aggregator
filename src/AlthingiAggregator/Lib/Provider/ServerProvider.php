<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/04/2016
 * Time: 5:27 PM
 */

namespace AlthingiAggregator\Lib\Provider;

use AlthingiAggregator\Lib\ClientAwareInterface;
use Zend\Http\Client;
use Zend\Http\Request;

class ServerProvider implements ProviderInterface, ClientAwareInterface
{
    /** @var  Client */
    private $client;

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
        $request = new Request();
        $request->setMethod('get')->setUri($url);
        $response = $this->client->send($request);

        $status = $response->getStatusCode();
        if ($status === 200) {
            $content = $response->getBody();

            if ($this->options['save']) {
                $this->saveFile($url, $content);
            }

            $dom = @new \DOMDocument();
            if ($dom->loadXML($content)) {
                return $dom;
            }
            throw new \Exception(print_r(error_get_last(), true));
        }

        throw new \Exception($response->getReasonPhrase());
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
}
