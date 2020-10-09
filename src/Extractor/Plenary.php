<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Plenary implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        if (! $object->hasAttribute('númer')) {
            throw new Extractor\Exception('Missing [{númer}] value', $object);
        }

        if (! $object->getElementsByTagName('fundarheiti')->item(0)) {
            throw new Extractor\Exception('Missing [{fundarheiti}] value', $object);
        }

        if (! $object->getElementsByTagName('fundursettur')->item(0)) {
            throw new Extractor\Exception('Missing [{fundursettur}] value', $object);
        }

        if (! $object->getElementsByTagName('fuslit')->item(0)) {
            throw new Extractor\Exception('Missing [{fuslit}] value', $object);
        }

        $this->setIdentity($object->getAttribute('númer'));

        $name = $object->getElementsByTagName('fundarheiti')->item(0)->nodeValue;
        $from = date('Y-m-d H:i', strtotime($object->getElementsByTagName('fundursettur')->item(0)->nodeValue));
        $to = date('Y-m-d H:i', strtotime($object->getElementsByTagName('fuslit')->item(0)->nodeValue));

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
