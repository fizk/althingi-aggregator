<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class Plenary implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array|null
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function extract(DOMElement $object)
    {
        if (! $object->hasAttribute('númer')) {
            throw new ModelException('Missing [{númer}] value', $object);
        }

        if (! $object->getElementsByTagName('fundarheiti')->item(0)) {
            throw new ModelException('Missing [{fundarheiti}] value', $object);
        }

        if (! $object->getElementsByTagName('fundursettur')->item(0)) {
            throw new ModelException('Missing [{fundursettur}] value', $object);
        }

        if (! $object->getElementsByTagName('fuslit')->item(0)) {
            throw new ModelException('Missing [{fuslit}] value', $object);
        }

        $this->setIdentity($object->getAttribute('númer'));

        $name = $object->getElementsByTagName('fundarheiti')->item(0)->nodeValue;
        $from = date('Y-m-d H:i', strtotime($object->getElementsByTagName('fundursettur')->item(0)->nodeValue));
        $to = date('Y-m-d H:i', strtotime($object->getElementsByTagName('fuslit')->item(0)->nodeValue));

        return [
            'plenary_id' => (int) $this->getIdentity(),
            'name' => $name,
            'from' => $from,
            'to' => $to,
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
