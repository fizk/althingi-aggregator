<?php
namespace App\Consumer\Stub;

use App\Extractor\ExtractionInterface;

class ExtractorValidPost implements ExtractionInterface
{
    private array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function extract(\DOMElement $object): array
    {
        return $this->data;
    }
}
