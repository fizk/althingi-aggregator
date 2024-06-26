<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class CommitteeMeeting implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): self
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws Extractor\Exception
     */
    public function extract(): array
    {
        if (! $this->object->hasAttribute('númer')) {
            throw new Extractor\Exception('Missing [{númer}] value', $this->object);
        }

        $this->setIdentity((int) $this->object->getAttribute('númer'));

        $from = ($this->object->getElementsByTagName('fundursettur')->item(0) &&
            ! empty($this->object->getElementsByTagName('fundursettur')->item(0)->nodeValue))
            ? date('Y-m-d H:i:s', strtotime($this->object->getElementsByTagName('fundursettur')->item(0)->nodeValue))
            : null ;

        $to = ($this->object->getElementsByTagName('fuslit')->item(0) &&
            ! empty($this->object->getElementsByTagName('fuslit')->item(0)->nodeValue))
            ? date('Y-m-d H:i:s', strtotime($this->object->getElementsByTagName('fuslit')->item(0)->nodeValue))
            : null ;

        $description = ($this->object->getElementsByTagName('fundargerð')->item(0) &&
            $this->object->getElementsByTagName('fundargerð')->item(0)->getElementsByTagName('texti')->item(0))
            ? trim($this->object->getElementsByTagName('fundargerð')
                ->item(0)->getElementsByTagName('texti')->item(0)->nodeValue)
            : null ;

        return [
            'from' => $from,
            'to' => $to,
            'description' => $description
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
