<?php
namespace App\Extractor;

use App\Lib\IdentityInterface;
use DOMElement;

class Inflation implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @throws \Exception
     */
    public function extract(DOMElement $object): array
    {
        $date = new \DateTime($object->getElementsByTagName('Date')->item(0)->nodeValue);

        $this->setIdentity((int) $date->format('Ymd'));

        return [
            'date' => $date->format('Y-m-d'),
            'value' => (float) $object->getElementsByTagName('Value')->item(0)->nodeValue,
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
