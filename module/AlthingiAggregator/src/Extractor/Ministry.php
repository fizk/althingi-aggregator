<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class Ministry implements ExtractionInterface, IdentityInterface
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

        $name = $object->getElementsByTagName('heiti')->length === 1
            ? trim($object->getElementsByTagName('heiti')->item(0)->nodeValue)
            : null;
        $abbrShort = $object->getElementsByTagName('stuttskammstöfun')->length === 1
            ? trim($object->getElementsByTagName('stuttskammstöfun')->item(0)->nodeValue)
            : null;
        $abbrLong = $object->getElementsByTagName('löngskammstöfun')->length === 1
            ? trim($object->getElementsByTagName('löngskammstöfun')->item(0)->nodeValue)
            : null;
        $first = $object->getElementsByTagName('fyrstaþing')->length === 1
            ? trim($object->getElementsByTagName('fyrstaþing')->item(0)->nodeValue)
            : null;
        $last = $object->getElementsByTagName('síðastaþing')->length === 1
            ? trim($object->getElementsByTagName('síðastaþing')->item(0)->nodeValue)
            : null;

        return [
            'ministry_id' => (int) $this->getIdentity(),
            'name' => $name,
            'abbr_short' => $abbrShort,
            'abbr_slong' => $abbrLong,
            'first' => empty($first) ? null : (int) $first,
            'last' => empty($last) ? null : (int) $last,
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
