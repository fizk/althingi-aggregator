<?php
namespace AlthingiAggregatorTest\Lib\Provider;

use AlthingiAggregator\Lib\Provider\ProviderInterface;

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
