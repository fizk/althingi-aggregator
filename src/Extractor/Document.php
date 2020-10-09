<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Document implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
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

    public function setIdentity(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }
}
