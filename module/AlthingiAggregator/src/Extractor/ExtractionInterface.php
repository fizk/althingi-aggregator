<?php
namespace AlthingiAggregator\Extractor;

interface ExtractionInterface
{
    /**
     * @param \DOMElement $object
     * @return array
     */
    public function extract(\DOMElement $object);
}
