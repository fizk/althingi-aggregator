<?php

namespace App\Extractor;

use DOMElement;

interface ExtractionInterface
{
    public function populate(DOMElement $object): static;
    public function extract(): array;
}
