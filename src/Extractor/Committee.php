<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Committee implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): self
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws Extractor\Exception
     */
    public function extract(): array
    {
        if (! $this->object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('heiti')->length) {
            throw new Extractor\Exception('Missing [{heiti}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('fyrstaþing')->length) {
            throw new Extractor\Exception('Missing [{fyrstaþing}] value', $this->object);
        }

        $this->setIdentity((int) $this->object->getAttribute('id'));

        $name = trim($this->object->getElementsByTagName('heiti')->item(0)->nodeValue);
        $firstAssemblyId = $this->object->getElementsByTagName('fyrstaþing')->item(0)->nodeValue;
        $lastAssemblyId = $this->object->getElementsByTagName('síðastaþing')?->item(0)?->nodeValue;
        $abbrShort = $this->object->getElementsByTagName('stuttskammstöfun')?->item(0)?->nodeValue;
        $abbrLong = $this->object->getElementsByTagName('löngskammstöfun')?->item(0)?->nodeValue;

        return [
            'committee_id' => $this->getIdentity(),
            'name' => $name,
            'first_assembly_id' => $firstAssemblyId ? (int) $firstAssemblyId : null,
            'last_assembly_id' => $lastAssemblyId ? (int) $lastAssemblyId : null,
            'abbr_short' => $abbrShort,
            'abbr_long' => $abbrLong,
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
