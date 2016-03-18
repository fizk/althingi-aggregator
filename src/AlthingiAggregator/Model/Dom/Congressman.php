<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/05/15
 * Time: 7:22 AM
 */

namespace AlthingiAggregator\Model\Dom;

use AlthingiAggregator\Lib\IdentityInterface;
use Zend\Stdlib\Extractor\ExtractionInterface;
use AlthingiAggregator\Model\Exception as ModelException;

class Congressman implements ExtractionInterface, IdentityInterface
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

        if (!$object->hasAttribute('id')) {
            throw new ModelException('Missing [id] value', $object);
        }

        if (!$object->getElementsByTagName('nafn')->item(0)) {
            throw new ModelException('Missing [{nafn}] value', $object);
        }

        $this->setIdentity($object->getAttribute('id'));
        $name = $object->getElementsByTagName('nafn')->item(0)->nodeValue;
        $birth = ($object->getElementsByTagName('fæðingardagur')->item(0))
            ? date('Y-m-d', strtotime($object->getElementsByTagName('fæðingardagur')->item(0)->nodeValue))
            : null ;

        return [
            'id' => (int) $this->getIdentity(),
            'name' => $name,
            'birth' => $birth,
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
