<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/03/2016
 * Time: 12:59 PM
 */

namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class Proponent implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function extract(DOMElement $object)
    {
        if (!$object->hasAttribute('id')) {
            throw new ModelException('Missing [{id}] value', $object);
        }

        if (!$object->hasAttribute('röð')) {
            throw new ModelException('Missing [{röð}] value', $object);
        }

        $this->setIdentity($object->getAttribute('id'));

        $minister = $object->getElementsByTagName('ráðherra')->length
            ? trim($object->getElementsByTagName('ráðherra')->item(0)->nodeValue)
            : null;

        return [
            'congressman_id' => (int) $object->getAttribute('id'),
            'order' => (int) $object->getAttribute('röð'),
            'minister' => $minister
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
