<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/04/2016
 * Time: 4:39 PM
 */

namespace AlthingiAggregator\Lib\Consumer;

use Zend\Hydrator\ExtractionInterface;
use DOMElement;

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
