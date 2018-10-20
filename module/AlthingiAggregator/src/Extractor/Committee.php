<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 5/04/2016
 * Time: 12:31 PM
 */

namespace AlthingiAggregator\Extractor;

use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class Committee implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * @param \DOMElement $object
     * @return array
     * @throws ModelException
     */
    public function extract(\DOMElement $object)
    {
        if (! $object->hasAttribute('id')) {
            throw new ModelException('Missing [{id}] value', $object);
        }

        if (! $object->getElementsByTagName('heiti')->length) {
            throw new ModelException('Missing [{heiti}] value', $object);
        }

        if (! $object->getElementsByTagName('fyrstaþing')->length) {
            throw new ModelException('Missing [{fyrstaþing}] value', $object);
        }

        $this->setIdentity((int) $object->getAttribute('id'));

        $name = trim($object->getElementsByTagName('heiti')->item(0)->nodeValue);
        $firstAssemblyId = (int) $object->getElementsByTagName('fyrstaþing')->item(0)->nodeValue;
        $lastAssemblyId = $object->getElementsByTagName('síðastaþing')->length
            ? (int) $object->getElementsByTagName('síðastaþing')->item(0)->nodeValue
            : null;
        $abbrShort = $object->getElementsByTagName('stuttskammstöfun')->length
            ? $object->getElementsByTagName('stuttskammstöfun')->item(0)->nodeValue
            : null;
        $abbrLong = $object->getElementsByTagName('löngskammstöfun')->length
            ? $object->getElementsByTagName('löngskammstöfun')->item(0)->nodeValue
            : null;

        return [
            'committee_id' => $this->getIdentity(),
            'name' => $name,
            'first_assembly_id' => $firstAssemblyId,
            'last_assembly_id' => $lastAssemblyId,
            'abbr_short' => $abbrShort,
            'abbr_long' => $abbrLong,
        ];
    }

    public function setIdentity($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity()
    {
        return $this->id;
    }
}
