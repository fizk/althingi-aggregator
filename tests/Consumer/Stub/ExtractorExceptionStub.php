<?php
namespace App\Consumer\Stub;

use App\Extractor\ExtractionInterface;
use App\Extractor;

class ExtractorExceptionStub implements ExtractionInterface
{
    public function extract(\DOMElement $object): array
    {
        throw new Extractor\Exception('Missing [{}] value');
    }
}
