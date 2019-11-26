<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor;

class Assembly implements ExtractionInterface, IdentityInterface
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
            throw new Extractor\Exception('Missing [{númer}] value', $object);
        }

        if (! $object->getElementsByTagName('þingsetning')->item(0)) {
            throw new Extractor\Exception('Missing [{þingsetning}] value', $object);
        }

        $this->setIdentity($object->getAttribute('númer'));

        $from = date(
            'Y-m-d',
            strtotime($object->getElementsByTagName('þingsetning')->item(0)->nodeValue)
        );
        $to = ($object->getElementsByTagName('þinglok')->item(0))
            ? date('Y-m-d', strtotime($object->getElementsByTagName('þinglok')->item(0)->nodeValue))
            : null ;

        return [
            'no' => (int) $this->getIdentity(),
            'from' => $from,
            'to' => $to
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
