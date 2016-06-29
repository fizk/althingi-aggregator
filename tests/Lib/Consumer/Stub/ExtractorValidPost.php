<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 1/06/2016
 * Time: 1:23 PM
 */

namespace AlthingiAggregator\Lib\Consumer\Stub;

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
