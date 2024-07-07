<?php

namespace App\Extractor;

use App\Lib\IdentityInterface;
use DOMElement;
use DateTime;

class Inflation implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): static
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function extract(): array
    {
        $date = new DateTime($this->object->getElementsByTagName('Date')->item(0)->nodeValue);

        $this->setIdentity((int) $date->format('Ymd'));

        return [
            'date' => $date->format('Y-m-d'),
            'value' => (float) $this->object->getElementsByTagName('Value')->item(0)->nodeValue,
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
