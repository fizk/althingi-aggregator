<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Document implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): self
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(): array
    {
        if (! $this->object->hasAttribute('málsnúmer')) {
            throw new Extractor\Exception('Missing [{málsnúmer}] value', $this->object);
        }

        if (! $this->object->hasAttribute('skjalsnúmer')) {
            throw new Extractor\Exception('Missing [{skjalsnúmer}] value', $this->object);
        }

        if (! $this->object->hasAttribute('þingnúmer')) {
            throw new Extractor\Exception('Missing [{þingnúmer}] value', $this->object);
        }

        $date = $this->object->getElementsByTagName('útbýting')->length
            ? date('Y-m-d H:i', strtotime($this->object->getElementsByTagName('útbýting')->item(0)->nodeValue))
            : null;
        $url = $this->object->getElementsByTagName('html')->length
            ? trim($this->object->getElementsByTagName('html')->item(0)->nodeValue)
            : null;
        $type = $this->object->getElementsByTagName('skjalategund')->item(0)
            ? trim($this->object->getElementsByTagName('skjalategund')->item(0)->nodeValue)
            : null;

        $this->setIdentity((int) $this->object->getAttribute('skjalsnúmer'));

        return [
            'issue_id' => (int) $this->object->getAttribute('málsnúmer'),
            'assembly_id' => (int) $this->object->getAttribute('þingnúmer'),
            'document_id' => (int) $this->object->getAttribute('skjalsnúmer'),
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
