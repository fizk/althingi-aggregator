<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor;

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
        if (! $object->hasAttribute('málsnúmer')) {
            throw new Extractor\Exception('Missing [{málsnúmer}] value', $object);
        }

        if (! $object->hasAttribute('skjalsnúmer')) {
            throw new Extractor\Exception('Missing [{skjalsnúmer}] value', $object);
        }

        if (! $object->hasAttribute('þingnúmer')) {
            throw new Extractor\Exception('Missing [{þingnúmer}] value', $object);
        }

        $date = $object->getElementsByTagName('útbýting')->length
            ? date('Y-m-d H:i', strtotime($object->getElementsByTagName('útbýting')->item(0)->nodeValue))
            : null;
        $url = $object->getElementsByTagName('html')->length
            ? trim($object->getElementsByTagName('html')->item(0)->nodeValue)
            : null;
        $type = $object->getElementsByTagName('skjalategund')->item(0)
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
