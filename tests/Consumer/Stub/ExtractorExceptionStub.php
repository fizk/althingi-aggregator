<?php
namespace App\Consumer\Stub;

use App\Extractor\ExtractionInterface;
use App\Extractor;
use DOMElement;

class ExtractorExceptionStub implements ExtractionInterface
{
    public function populate(DOMElement $object): self
    {
        return $this;
    }

    public function extract(): array
    {
        throw new Extractor\Exception('Missing [{}] value');
    }
}
