<?php

namespace App\Provider;

use App\Provider\ProviderInterface;
use DOMDocument;

class NullProvider implements ProviderInterface
{
    public function get(string $url, callable $cb = null): DOMDocument
    {
        return new DOMDocument();
    }
}
