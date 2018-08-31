<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 1/06/2016
 * Time: 1:23 PM
 */

namespace AlthingiAggregatorTest\Lib\Consumer\Stub;

use AlthingiAggregator\Extractor\ExtractionInterface;
use AlthingiAggregator\Lib\IdentityInterface;

class ExtractorValidPut implements ExtractionInterface, IdentityInterface
{
    private $data = [];

    private $id = 1;

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

    public function setIdentity($id)
    {
        $this->id = $id;
    }

    public function getIdentity()
    {
        return $this->id;
    }
}
