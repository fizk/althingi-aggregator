<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Party implements ExtractionInterface, IdentityInterface
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
        $name = $object->getElementsByTagName('heiti')->item(0)->nodeValue;
        $abbrShort = $object->getElementsByTagName('stuttskammstöfun')->item(0)->nodeValue;
        $abbrLong = $object->getElementsByTagName('löngskammstöfun')->item(0)->nodeValue . PHP_EOL;

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
