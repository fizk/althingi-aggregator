<?php
namespace App\Provider;

use App\Provider\ProviderInterface;
use DOMDocument;

class TestProvider implements ProviderInterface
{
    private array $documents = [];

    public function get(string $url, callable $cb = null): DOMDocument
    {
        return array_key_exists($url, $this->documents)
            ? $this->documents[$url]
            : null ;
    }

    public function addDocument($key, DOMDocument $document)
    {
        $this->documents[$key] = $document;
        return $this;
    }
}
