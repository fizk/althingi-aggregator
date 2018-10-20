<?php
namespace AlthingiAggregator\Lib;

use Zend\Uri\Http;

interface UriAwareInterface
{
    public function setUri(Http $uri);
}
