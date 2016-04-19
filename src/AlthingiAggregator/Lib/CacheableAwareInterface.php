<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 12/04/2016
 * Time: 6:21 PM
 */

namespace AlthingiAggregator\Lib;

use Zend\Cache\Storage\StorageInterface;

interface CacheableAwareInterface
{
    public function setCache(StorageInterface $cache);
}
