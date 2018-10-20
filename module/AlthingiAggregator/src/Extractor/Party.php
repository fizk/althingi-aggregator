<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class Party implements ExtractionInterface, IdentityInterface
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
        if (! $object->hasAttribute('id')) {
            throw new ModelException('Missing [{id}] value', $object);
        }

        $this->setIdentity($object->getAttribute('id'));
        $name = $object->getElementsByTagName('heiti')->item(0)->nodeValue;
        $abbrShort = $object->getElementsByTagName('stuttskammstöfun')->item(0)->nodeValue;
        $abbrLong = $object->getElementsByTagName('löngskammstöfun')->item(0)->nodeValue . PHP_EOL;

        return [
            'id' => (int) $this->getIdentity(),
            'name' => trim($name),
            'abbr_short' => trim($abbrShort),
            'abbr_long' => trim($abbrLong)
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
