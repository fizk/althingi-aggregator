<?php
namespace AlthingiAggregatorTest\Consumer;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\ExtractionInterface;
use AlthingiAggregator\Consumer\ConsumerInterface;

class TestConsumer implements ConsumerInterface, \Countable
{

    private $objects = [];

    private $count = 0;

    /**
     * Save $extract to storage/consumer.
     *
     * @param DOMElement $element
     * @param string $storageKey
     * @param ExtractionInterface $extract
     * @return mixed
     */
    public function save(DOMElement $element, $storageKey, ExtractionInterface $extract)
    {
        $result = $extract->extract($element);
        $key = ($extract instanceof IdentityInterface)
            ? sprintf('%s/%s', $storageKey, $extract->getIdentity())
            : $storageKey ;

        ++$this->count;
        $this->objects[$key] = $result;

        return;
    }

    public function getObjects()
    {
        return $this->objects;
    }

    public function count()
    {
        return $this->count;
    }
}
