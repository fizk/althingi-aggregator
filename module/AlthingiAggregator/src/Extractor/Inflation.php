<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;

class Inflation implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * Extract values from an object
     *
     * @param DOMElement $object
     * @return array|null
     * @throws \Exception
     */
    public function extract(DOMElement $object)
    {
        $date = new \DateTime($object->getElementsByTagName('Date')->item(0)->nodeValue);

        $this->setIdentity((int) $date->format('Ymd'));

        return [
            'date' => $date->format('Y-m-d'),
            'value' => (float) $object->getElementsByTagName('Value')->item(0)->nodeValue,
        ];
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
