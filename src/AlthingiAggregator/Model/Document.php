<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 10:34 AM
 */

namespace AlthingiAggregator\Model;

use AlthingiAggregator\Lib\IdentityInterface;
use Zend\Hydrator\ExtractionInterface;
use AlthingiAggregator\Model\Exception as ModelException;

class Document implements ExtractionInterface, IdentityInterface
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

        if (!$object->hasAttribute('málsnúmer')) {
            throw new ModelException('Missing [{málsnúmer}] value', $object);
        }

        if (!$object->hasAttribute('skjalsnúmer')) {
            throw new ModelException('Missing [{skjalsnúmer}] value', $object);
        }

        if (!$object->hasAttribute('þingnúmer')) {
            throw new ModelException('Missing [{þingnúmer}] value', $object);
        }

        if (!$object->getElementsByTagName('skjalategund')->item(0)) {
            throw new ModelException('Missing [{skjalategund}] value', $object);
        }

        if (!$object->getElementsByTagName('html')->item(0)) {
            throw new ModelException('Missing [{html}] value', $object);
        }


        $this->setIdentity((int) $object->getAttribute('skjalsnúmer'));

        return [
            'issue_id' => (int) $object->getAttribute('málsnúmer'),
            'assembly_id' => (int) $object->getAttribute('þingnúmer'),
            'document_id' => (int) $object->getAttribute('skjalsnúmer'),
            'type' => trim($object->getElementsByTagName('skjalategund')->item(0)->nodeValue),
            'date' => date('Y-m-d H:i:s', strtotime($object->getElementsByTagName('útbýting')->item(0)->nodeValue)),
            'url' => trim($object->getElementsByTagName('html')->item(0)->nodeValue)
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
