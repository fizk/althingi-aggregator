<?php

namespace App\Consumer\Stub;

use App\Extractor\ExtractionInterface;
use DOMElement;

class ExtractorValidPost implements ExtractionInterface
{
    private array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function populate(DOMElement $object): static
    {
        return $this;
    }

    public function extract(): array
    {
        return $this->data;
    }
}
