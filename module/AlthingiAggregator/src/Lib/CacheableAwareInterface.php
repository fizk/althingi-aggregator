<?php
namespace AlthingiAggregator\Lib;

use Zend\Cache\Storage\StorageInterface;

interface CacheableAwareInterface
{
    public function setCache(StorageInterface $cache);
}
