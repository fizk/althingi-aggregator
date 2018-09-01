<?php
namespace AlthingiAggregatorTest\Lib\Consumer\Stub;

use AlthingiAggregator\Extractor\ExtractionInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class ExtractorExceptionStub implements ExtractionInterface
{

    /**
     * @param \DOMElement $object
     * @return array
     * @throws ModelException
     */
    public function extract(\DOMElement $object)
    {
        throw new ModelException('Missing [{}] value');
    }
}
