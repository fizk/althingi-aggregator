<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/04/2016
 * Time: 8:58 PM
 */

namespace AlthingiAggregator\Lib\Consumer;

use AlthingiAggregator\Lib\IdentityInterface;
use DOMElement;
use Zend\Hydrator\ExtractionInterface;

class TestConsumer implements ConsumerInterface
{

    private $objects = [];

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

        $this->objects[$key] = $result;
    }

    public function getObjects()
    {
        return $this->objects;
    }
}
