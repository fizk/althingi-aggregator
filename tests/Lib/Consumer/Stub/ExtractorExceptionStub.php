<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 1/06/2016
 * Time: 1:16 PM
 */
namespace AlthingiAggregator\Lib\Consumer\Stub;

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
