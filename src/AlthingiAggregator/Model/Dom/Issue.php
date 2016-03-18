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

class Issue implements ExtractionInterface, IdentityInterface
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

        if (!$object->hasAttribute('málsnúmer')) {
            throw new ModelException('Missing [{málsnúmer}] value', $object);
        }

        if (!$object->hasAttribute('þingnúmer')) {
            throw new ModelException('Missing [{þingnúmer}] value', $object);
        }

        //if (!$object->hasAttribute('málsflokkur')) {
        //    throw new ModelException('Missing [{málsflokkur}] value');
        //}

        if (!$object->getElementsByTagName('málsheiti')->item(0)) {
            throw new ModelException('Missing [{málsheiti}] value', $object);
        }

        if (!$object->getElementsByTagName('staðamáls')->item(0)) {
            //throw new ModelException('Missing [{staðamáls}] value', $object);
        }

        if (!$object->getElementsByTagName('málstegund')->item(0)) {
            throw new ModelException('Missing [{málstegund}] value', $object);
        }

        if (!$object->getElementsByTagName('málstegund')->item(0)->hasAttribute('málstegund')) {
            throw new ModelException('Missing [{málstegund}] value', $object);
        }

        if (!$object->getElementsByTagName('málstegund')->item(0)->getElementsByTagName('heiti')->item(0)) {
            throw new ModelException('Missing [{heiti}] value', $object);
        }

        if (!$object->getElementsByTagName('málstegund')->item(0)->getElementsByTagName('heiti2')->item(0)) {
            throw new ModelException('Missing [{heiti2}] value', $object);
        }

        //----


        $this->setIdentity($object->getAttribute('málsnúmer'));

        return [
            'id' => (int) $this->getIdentity(),
            'assembly_id' => (int) $object->getAttribute('þingnúmer'),
            'category' => 'A',//$object->getAttribute('málsflokkur'),
            'status' => ($object->getElementsByTagName('staðamáls')->item(0))
                ? $object->getElementsByTagName('staðamáls')->item(0)->nodeValue
                : null ,
            'name' => $object->getElementsByTagName('málsheiti')->item(0)->nodeValue,
            'type' => $object->getElementsByTagName('málstegund')->item(0)->hasAttribute('málstegund'),
            'type_name' => $object->getElementsByTagName('málstegund')
                ->item(0)->getElementsByTagName('heiti')->item(0)->nodeValue,
            'type_subname' => $object->getElementsByTagName('málstegund')
                ->item(0)->getElementsByTagName('heiti2')->item(0)->nodeValue,
//            'congressman_id' => $object->hasAttribute('þingmaður')
//                ? $object->getAttribute('þingmaður')
//                : null
                //TODO this should not be here
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
