<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Plenary implements ExtractionInterface, IdentityInterface
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
        if (! $this->object->hasAttribute('númer')) {
            throw new Extractor\Exception('Missing [{númer}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('fundarheiti')->item(0)) {
            throw new Extractor\Exception('Missing [{fundarheiti}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('fundursettur')->item(0)) {
            throw new Extractor\Exception('Missing [{fundursettur}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('fuslit')->item(0)) {
            throw new Extractor\Exception('Missing [{fuslit}] value', $this->object);
        }

        $this->setIdentity($this->object->getAttribute('númer'));

        $name = $this->object->getElementsByTagName('fundarheiti')->item(0)->nodeValue;
        $from = date('Y-m-d H:i', strtotime($this->object->getElementsByTagName('fundursettur')->item(0)->nodeValue));
        $to = date('Y-m-d H:i', strtotime($this->object->getElementsByTagName('fuslit')->item(0)->nodeValue));

        return [
            'plenary_id' => (int) $this->getIdentity(),
            'name' => $name,
            'from' => $from,
            'to' => $to,
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
