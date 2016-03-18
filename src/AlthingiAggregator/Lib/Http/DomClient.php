<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/05/15
 * Time: 7:01 AM
 */

namespace AlthingiAggregator\Lib\Http;

use Zend\Http\Request;
use Zend\Http\Client;

class DomClient
{
    /**
     * @var \Zend\Http\Client
     */
    private $client;

    public function get($url)
    {
        $request = new Request();
        $request->setMethod('get')->setUri($url);
        $response = $this->getClient()->send($request);

        $status = $response->getStatusCode();
        if ($status === 200) {
            $content = $response->getBody();

            $dom = @new \DOMDocument();
            if ($dom->loadXML($content)) {
                return $dom;
            }
            throw new \Exception(print_r(error_get_last(), true));
        }

        throw new \Exception($response->getReasonPhrase());
    }

    /**
     * @return \Zend\Http\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }
}
