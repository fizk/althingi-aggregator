<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class CommitteeMeeting implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): static
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws Extractor\Exception
     */
    public function extract(): array
    {
        if (! $this->object->hasAttribute('númer')) {
            throw new Extractor\Exception('Missing [{númer}] value', $this->object);
        }

        $this->setIdentity((int) $this->object->getAttribute('númer'));

        $from = $this->object->getElementsByTagName('dagurtími')?->item(0)
            ? date('Y-m-d H:i:s', strtotime($this->object->getElementsByTagName('dagurtími')?->item(0)->nodeValue))
            : null;

        if (!$from) {
            $from = $this->object->getElementsByTagName('fundursettur')?->item(0)
                ? date('Y-m-d H:i:s', strtotime($this->object->getElementsByTagName('fundursettur')?->item(0)->nodeValue))
                : null;
        }

        if (!$from) {
            $from = $this->object->getElementsByTagName('dagur')?->item(0)
                ? date('Y-m-d H:i:s', strtotime($this->object->getElementsByTagName('dagur')?->item(0)->nodeValue . ' 00:00:00'))
                : null;
        }

        $to = $this->object->getElementsByTagName('fuslit')?->item(0)
            ? date('Y-m-d H:i:s', strtotime($this->object->getElementsByTagName('fuslit')?->item(0)->nodeValue))
            : null;


        $description = $this->object->getElementsByTagName('fundargerð')
            ?->item(0)
            ?->getElementsByTagName('texti')
            ?->item(0)
            ?->nodeValue;

        $type = $this->object->getElementsByTagName('tegundFundar')?->item(0)?->nodeValue;
        $place = $this->object->getElementsByTagName('staður')?->item(0)?->nodeValue;

        return [
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'place' => $place,
            'description' => $description
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
