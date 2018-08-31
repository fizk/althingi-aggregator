<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 10:34 AM
 */

namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class Document implements ExtractionInterface, IdentityInterface
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
        if (!$object->hasAttribute('málsnúmer')) {
            throw new ModelException('Missing [{málsnúmer}] value', $object);
        }

        if (!$object->hasAttribute('skjalsnúmer')) {
            throw new ModelException('Missing [{skjalsnúmer}] value', $object);
        }

        if (!$object->hasAttribute('þingnúmer')) {
            throw new ModelException('Missing [{þingnúmer}] value', $object);
        }

        $date = $object->getElementsByTagName('útbýting')->length
            ? date('Y-m-d H:i', strtotime($object->getElementsByTagName('útbýting')->item(0)->nodeValue))
            : null;
        $url = $object->getElementsByTagName('html')->length
            ? trim($object->getElementsByTagName('html')->item(0)->nodeValue)
            : null;
        $type =  $object->getElementsByTagName('skjalategund')->item(0)
            ? trim($object->getElementsByTagName('skjalategund')->item(0)->nodeValue)
            : null;


        $this->setIdentity((int) $object->getAttribute('skjalsnúmer'));

        return [
            'issue_id' => (int) $object->getAttribute('málsnúmer'),
            'assembly_id' => (int) $object->getAttribute('þingnúmer'),
            'document_id' => (int) $object->getAttribute('skjalsnúmer'),
            'type' => $type,
            'date' => $date,
            'url' => $url
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
