<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Assembly implements ExtractionInterface, IdentityInterface
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

        if (! $object->getElementsByTagName('þingsetning')->item(0)) {
            throw new Extractor\Exception('Missing [{þingsetning}] value', $object);
        }

        $this->setIdentity($object->getAttribute('númer'));

        $from = date(
            'Y-m-d',
            strtotime($object->getElementsByTagName('þingsetning')->item(0)->nodeValue)
        );
        $to = ($object->getElementsByTagName('þinglok')->item(0))
            ? date('Y-m-d', strtotime($object->getElementsByTagName('þinglok')->item(0)->nodeValue))
            : null ;

        return [
            'no' => (int) $this->getIdentity(),
            'from' => $from,
            'to' => $to
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
