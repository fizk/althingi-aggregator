<?php
namespace AlthingiAggregator\Lib\Provider;

interface ProviderInterface
{
    /**
     * @param string $url
     * @return \DOMDocument
     */
    public function get($url);
}
