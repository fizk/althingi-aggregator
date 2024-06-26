<?php

namespace App\Provider;

use DOMDocument;

interface ProviderInterface
{
    public function get(string $url, callable $cb = null): DOMDocument;
}
