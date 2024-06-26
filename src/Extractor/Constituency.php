<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Constituency implements ExtractionInterface, IdentityInterface
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

        $this->setIdentity((int) $this->object->getAttribute('id'));

        $name = $this->object->getElementsByTagName('heiti')?->item(0)?->nodeValue;
        $description = $this->object->getElementsByTagName('lýsing')?->item(0)?->nodeValue;
        $abbr_short = $this->object->getElementsByTagName('stuttskammstöfun')?->item(0)?->nodeValue;
        $abbr_long = $this->object->getElementsByTagName('löngskammstöfun')?->item(0)?->nodeValue;

        return [
            'id' => $this->getIdentity(),
            'name' => $name ? trim($name) : null,
            'description' => $description ? trim($description) : null,
            'abbr_short' => $abbr_short ? trim($abbr_short) : null,
            'abbr_long' => $abbr_long ? trim($abbr_long) : null,
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
