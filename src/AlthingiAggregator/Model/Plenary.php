<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/05/15
 * Time: 7:22 AM
 */

namespace AlthingiAggregator\Model;

use AlthingiAggregator\Lib\IdentityInterface;
use Zend\Hydrator\ExtractionInterface;
use AlthingiAggregator\Model\Exception as ModelException;

class Plenary implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * Extract values from an object
     *
     * @param  \DOMElement $object
     * @return array|null
     * @throws \AlthingiAggregator\Model\Exception
     */
    public function extract($object)
    {
        if (!$object instanceof \DOMElement) {
            throw new ModelException('Not a valid \DOMElement');
        }

        if (!$object->hasAttribute('númer')) {
            throw new ModelException('Missing [{númer}] value', $object);
        }

        if (!$object->getElementsByTagName('fundarheiti')->item(0)) {
            throw new ModelException('Missing [{fundarheiti}] value', $object);
        }

        if (!$object->getElementsByTagName('fundursettur')->item(0)) {
            throw new ModelException('Missing [{fundursettur}] value', $object);
        }

        if (!$object->getElementsByTagName('fuslit')->item(0)) {
            throw new ModelException('Missing [{fuslit}] value', $object);
        }

        $this->setIdentity($object->getAttribute('númer'));

        return [
            'plenary_id' => (int) $this->getIdentity(),
            'name' => $object->getElementsByTagName('fundarheiti')->item(0)->nodeValue,
            'from' => date('Y-m-d H:i:s', strtotime($object->getElementsByTagName('fundursettur')->item(0)->nodeValue)),
            'to' => date('Y-m-d H:i:s', strtotime($object->getElementsByTagName('fuslit')->item(0)->nodeValue)),
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
