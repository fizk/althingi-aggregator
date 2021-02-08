<?php
namespace App\Extractor;

use DOMElement;

interface ExtractionInterface
{
    public function populate(DOMElement $object): self;
    public function extract(): array;
}
