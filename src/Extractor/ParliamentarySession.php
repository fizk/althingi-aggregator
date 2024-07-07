<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class ParliamentarySession implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): static
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(): array
    {
        if (! $this->object->hasAttribute('númer')) {
            throw new Extractor\Exception('Missing [{númer}] value', $this->object);
        }

        $id = $this->object->getAttribute('númer');

        $this->setIdentity(intval($id));

        $name = $this->object->getElementsByTagName('fundarheiti')?->item(0)?->nodeValue;
        $fromString = $this->object->getElementsByTagName('fundursettur')?->item(0)?->nodeValue;
        $toString = $this->object->getElementsByTagName('fuslit')?->item(0)?->nodeValue;
        $from = $fromString !== null
            ? date('Y-m-d H:i', strtotime($fromString))
            : null ;
        $to = $toString !== null
            ? date('Y-m-d H:i', strtotime($toString))
            : null;

        return [
            'parliamentary_session_id' => $this->getIdentity(),
            'name' => $name,
            'from' => $from,
            'to' => $to,
        ];
    }

    public function setIdentity(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }
}
