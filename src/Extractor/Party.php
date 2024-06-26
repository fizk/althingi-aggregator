<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Party implements ExtractionInterface, IdentityInterface
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
        if (! $this->object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $this->object);
        }

        $this->setIdentity($this->object->getAttribute('id'));
        $name = $this->object->getElementsByTagName('heiti')->item(0)->nodeValue;
        $abbrShort = $this->object->getElementsByTagName('stuttskammstöfun')->item(0)->nodeValue;
        $abbrLong = $this->object->getElementsByTagName('löngskammstöfun')->item(0)->nodeValue . PHP_EOL;

        return [
            'id' => (int) $this->getIdentity(),
            'name' => trim($name),
            'abbr_short' => trim($abbrShort),
            'abbr_long' => trim($abbrLong)
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
