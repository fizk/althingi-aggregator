<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 5/06/2016
 * Time: 5:26 PM
 */

namespace AlthingiAggregator\Lib;

use Zend\Uri\Http;

interface UriAwareInterface
{
    public function setUri(Http $uri);
}
