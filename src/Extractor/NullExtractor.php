<?php
namespace App\Extractor;

use App\Lib\IdentityInterface;
use DOMElement;

class NullExtractor implements ExtractionInterface, IdentityInterface
{
    private string $id;

    public function populate(DOMElement $object): self
    {
        return $this;
    }

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(): array
    {
        return [];
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
