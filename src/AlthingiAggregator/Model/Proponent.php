<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/03/2016
 * Time: 12:59 PM
 */

namespace AlthingiAggregator\Model;

use AlthingiAggregator\Lib\IdentityInterface;
use Zend\Hydrator\ExtractionInterface;
use AlthingiAggregator\Model\Exception as ModelException;

class Proponent implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     * @throws \AlthingiAggregator\Model\Exception
     */
    public function extract($object)
    {
        if (!$object instanceof \DOMElement) {
            throw new ModelException('Not a valid \DOMElement');
        }

        if (!$object->hasAttribute('id')) {
            throw new ModelException('Missing [{id}] value', $object);
        }

        if (!$object->hasAttribute('röð')) {
            throw new ModelException('Missing [{röð}] value', $object);
        }

        $this->setIdentity($object->getAttribute('id'));

        return [
            'congressman_id' => (int) $object->getAttribute('id'),
            'order' => (int) $object->getAttribute('röð')
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
