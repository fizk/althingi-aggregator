<?php
namespace App\Extractor;

use DOMElement;

interface ExtractionInterface
{
    public function extract(DOMElement $object): array;
}
