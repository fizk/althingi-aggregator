<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Ministry implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        if (! $object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $object);
        }

        $this->setIdentity($object->getAttribute('id'));

        $name = $object->getElementsByTagName('heiti')->length === 1
            ? trim($object->getElementsByTagName('heiti')->item(0)->nodeValue)
            : null;
        $abbrShort = $object->getElementsByTagName('stuttskammstöfun')->length === 1
            ? trim($object->getElementsByTagName('stuttskammstöfun')->item(0)->nodeValue)
            : null;
        $abbrLong = $object->getElementsByTagName('löngskammstöfun')->length === 1
            ? trim($object->getElementsByTagName('löngskammstöfun')->item(0)->nodeValue)
            : null;
        $first = $object->getElementsByTagName('fyrstaþing')->length === 1
            ? trim($object->getElementsByTagName('fyrstaþing')->item(0)->nodeValue)
            : null;
        $last = $object->getElementsByTagName('síðastaþing')->length === 1
            ? trim($object->getElementsByTagName('síðastaþing')->item(0)->nodeValue)
            : null;

        return [
            'ministry_id' => (int) $this->getIdentity(),
            'name' => $name,
            'abbr_short' => $abbrShort,
            'abbr_long' => $abbrLong,
            'first' => empty($first) ? null : (int) $first,
            'last' => empty($last) ? null : (int) $last,
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