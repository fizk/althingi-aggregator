<?php

namespace App\Lib;

use Laminas\Cache\Storage\StorageInterface;

interface CacheableAwareInterface
{
    public function setCache(StorageInterface $cache): static;
}
