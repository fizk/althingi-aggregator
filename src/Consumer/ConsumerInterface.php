<?php
namespace App\Consumer;

use App\Extractor\ExtractionInterface;
use DOMElement;

interface ConsumerInterface
{
    public function save(DOMElement $element, string $storageKey, ExtractionInterface $extract);
}
