<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 4/04/2016
 * Time: 5:34 PM
 */

namespace AlthingiAggregator\Extractor;

interface ExtractionInterface
{
    /**
     * @param \DOMElement $object
     * @return array
     */
    public function extract(\DOMElement $object);
}
