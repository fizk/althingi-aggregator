<?php
namespace AlthingiAggregator\Consumer;

use DOMElement;
use AlthingiAggregator\Extractor\ExtractionInterface;

interface ConsumerInterface
{
    /**
     * Save $extract to storage/consumer.
     *
     * @param DOMElement $element
     * @param string $storageKey
     * @param ExtractionInterface $extract
     * @return mixed
     */
    public function save(DOMElement $element, $storageKey, ExtractionInterface $extract);
}
