<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Ministry implements ExtractionInterface, IdentityInterface
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
        if (! $this->object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $this->object);
        }

        $this->setIdentity($this->object->getAttribute('id'));

        $name = $this->object->getElementsByTagName('heiti')?->item(0)?->nodeValue;
        $abbrShort = $this->object->getElementsByTagName('stuttskammstöfun')?->item(0)?->nodeValue;
        $abbrLong = $this->object->getElementsByTagName('löngskammstöfun')?->item(0)?->nodeValue;
        $first = $this->object->getElementsByTagName('fyrstaþing')?->item(0)?->nodeValue;
        $last = $this->object->getElementsByTagName('síðastaþing')?->item(0)?->nodeValue;

        return [
            'ministry_id' => (int) $this->getIdentity(),
            'name' => $name ? trim($name) : null,
            'abbr_short' => $abbrShort ? trim($abbrShort) : null,
            'abbr_long' => $abbrLong ? trim($abbrLong) : null,
            'first' => empty($first) ? null : (int) $first,
            'last' => empty($last) ? null : (int) $last,
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
