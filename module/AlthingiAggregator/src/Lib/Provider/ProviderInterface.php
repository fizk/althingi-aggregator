<?php
namespace AlthingiAggregator\Lib\Provider;

interface ProviderInterface
{
    /**
     * @param string $url
     * @param callable $cb
     * @return \DOMDocument
     */
    public function get($url, callable $cb = null);
}
