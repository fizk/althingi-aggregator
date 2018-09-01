<?php
namespace AlthingiAggregatorTest\Lib\Consumer\Stub;

use AlthingiAggregator\Extractor\ExtractionInterface;

class ExtractorValidPost implements ExtractionInterface
{
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param \DOMElement $object
     * @return array
     */
    public function extract(\DOMElement $object)
    {
        return $this->data;
    }
}
