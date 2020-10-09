<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Committee implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @throws Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        if (! $object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $object);
        }

        if (! $object->getElementsByTagName('heiti')->length) {
            throw new Extractor\Exception('Missing [{heiti}] value', $object);
        }

        if (! $object->getElementsByTagName('fyrstaþing')->length) {
            throw new Extractor\Exception('Missing [{fyrstaþing}] value', $object);
        }

        $this->setIdentity((int) $object->getAttribute('id'));

        $name = trim($object->getElementsByTagName('heiti')->item(0)->nodeValue);
        $firstAssemblyId = (int) $object->getElementsByTagName('fyrstaþing')->item(0)->nodeValue;
        $lastAssemblyId = $object->getElementsByTagName('síðastaþing')->length
            ? (int) $object->getElementsByTagName('síðastaþing')->item(0)->nodeValue
            : null;
        $abbrShort = $object->getElementsByTagName('stuttskammstöfun')->length
            ? $object->getElementsByTagName('stuttskammstöfun')->item(0)->nodeValue
            : null;
        $abbrLong = $object->getElementsByTagName('löngskammstöfun')->length
            ? $object->getElementsByTagName('löngskammstöfun')->item(0)->nodeValue
            : null;

        return [
            'committee_id' => $this->getIdentity(),
            'name' => $name,
            'first_assembly_id' => $firstAssemblyId,
            'last_assembly_id' => $lastAssemblyId,
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
