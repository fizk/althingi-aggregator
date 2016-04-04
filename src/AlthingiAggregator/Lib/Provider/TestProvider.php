<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/04/2016
 * Time: 9:00 PM
 */

namespace AlthingiAggregator\Lib\Provider;

class TestProvider implements ProviderInterface
{

    private $documents = [];
    /**
     * @param string $url
     * @return \DOMDocument
     */
    public function get($url)
    {
        return array_key_exists($url, $this->documents)
            ? $this->documents[$url]
            : null ;
    }

    public function addDocument($key, \DOMDocument $document)
    {
        $this->documents[$key] = $document;
        return $this;
    }
}
