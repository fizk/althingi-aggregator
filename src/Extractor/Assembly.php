<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Assembly implements ExtractionInterface, IdentityInterface
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

        if (! $this->object->getElementsByTagName('þingsetning')->item(0)) {
            throw new Extractor\Exception('Missing [{þingsetning}] value', $this->object);
        }

        $this->setIdentity($this->object->getAttribute('númer'));

        $from = date(
            'Y-m-d',
            strtotime($this->object->getElementsByTagName('þingsetning')->item(0)->nodeValue)
        );
        $to = ($this->object->getElementsByTagName('þinglok')->item(0))
            ? date('Y-m-d', strtotime($this->object->getElementsByTagName('þinglok')->item(0)->nodeValue))
            : null ;

        return [
            'no' => (int) $this->getIdentity(),
            'from' => $from,
            'to' => $to
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
