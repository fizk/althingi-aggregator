<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/04/2016
 * Time: 5:23 PM
 */

namespace AlthingiAggregator\Lib\Provider;

interface ProviderInterface
{
    /**
     * @param string $url
     * @return \DOMDocument
     */
    public function get($url);
}
