<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Congressman implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        // throw new Extractor\Exception('Missing [id] value', $object);
        if (! $object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [id] value', $object);
        }

        if (! $object->getElementsByTagName('nafn')->item(0)) {
            throw new Extractor\Exception('Missing [{nafn}] value', $object);
        }

        $this->setIdentity($object->getAttribute('id'));

        $name = $object->getElementsByTagName('nafn')->item(0)->nodeValue;
        $abbreviation = $object->getElementsByTagName('skammstöfun')->item(0)
            ? $object->getElementsByTagName('skammstöfun')->item(0)->nodeValue
            : null;
        $birth = ($object->getElementsByTagName('fæðingardagur')->item(0))
            ? date('Y-m-d', strtotime($object->getElementsByTagName('fæðingardagur')->item(0)->nodeValue))
            : null ;
        //TODO isn't there suppose to be a death date

        return [
            'id' => (int) $this->getIdentity(),
            'name' => $name,
            'birth' => $birth,
            'abbreviation' => $abbreviation,
            'death' => '' //TODO look into why this can't be null;
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
