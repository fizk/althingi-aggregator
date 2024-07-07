<?php

namespace App\Extractor;

use App\Lib\IdentityInterface;
use DOMElement;
use DateTime;

class Government implements ExtractionInterface, IdentityInterface
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
        $id = (new DateTime($this->object->getAttribute('from')))->format('Ymd');
        $this->setIdentity($id);

        return [
            'from' => $this->object->hasAttribute('from') ? $this->object->getAttribute('from') : null,
            'to' => $this->object->hasAttribute('to') ? $this->object->getAttribute('to') : null,
            'title' => trim($this->object->nodeValue)
        ];
    }

    public function setIdentity($id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }
}
