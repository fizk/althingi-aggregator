<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Congressman implements ExtractionInterface, IdentityInterface
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
        // throw new Extractor\Exception('Missing [id] value', $object);
        if (! $this->object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [id] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('nafn')->item(0)) {
            throw new Extractor\Exception('Missing [{nafn}] value', $this->object);
        }

        $this->setIdentity($this->object->getAttribute('id'));

        $name = $this->object->getElementsByTagName('nafn')->item(0)->nodeValue;
        $abbreviation = $this->object->getElementsByTagName('skammstöfun')?->item(0)?->nodeValue;
        $birth = ($this->object->getElementsByTagName('fæðingardagur')->item(0))
            ? date('Y-m-d', strtotime($this->object->getElementsByTagName('fæðingardagur')->item(0)->nodeValue))
            : null ;

        return [
            'id' => (int) $this->getIdentity(),
            'name' => $name,
            'birth' => $birth,
            'abbreviation' => $abbreviation,
            'death' => ''
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
