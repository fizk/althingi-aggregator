<?php
namespace AlthingiAggregatorTest\Consumer\Stub;

use AlthingiAggregator\Extractor\ExtractionInterface;
use AlthingiAggregator\Extractor;

class ExtractorExceptionStub implements ExtractionInterface
{

    /**
     * @param \DOMElement $object
     * @return array
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function extract(\DOMElement $object)
    {
        throw new Extractor\Exception('Missing [{}] value');
    }
}
