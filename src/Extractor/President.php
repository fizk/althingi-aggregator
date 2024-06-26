<?php

namespace App\Extractor;

use App\Extractor;
use DOMElement;

class President implements ExtractionInterface
{
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
        if (! $this->object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('nafn')->item(0)) {
            throw new Extractor\Exception('Missing [{nafn}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('þing')->item(0)) {
            throw new Extractor\Exception('Missing [{þing}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('inn')->item(0)) {
            throw new Extractor\Exception('Missing [{þing}] value', $this->object);
        }

        $from = date(
            'Y-m-d',
            strtotime($this->object->getElementsByTagName('inn')->item(0)->nodeValue)
        );
        $to = ($this->object->getElementsByTagName('út')->item(0))
            ? date('Y-m-d', strtotime($this->object->getElementsByTagName('út')->item(0)->nodeValue))
            : null ;

        return [
            'assembly_id' => (int) $this->object->getElementsByTagName('þing')->item(0)->nodeValue,
            'congressman_id' => (int) $this->object->getAttribute('id'),
            'title' => $this->object->getElementsByTagName('embættisheiti')->item(0)
                ? $this->object->getElementsByTagName('embættisheiti')->item(0)->nodeValue
                : '',
            'attr' => $this->object->getElementsByTagName('skammstöfun')->item(0)
                ? $this->object->getElementsByTagName('skammstöfun')->item(0)->nodeValue
                : '',
            'from' => $from,
            'to' => $to
        ];
    }
}
