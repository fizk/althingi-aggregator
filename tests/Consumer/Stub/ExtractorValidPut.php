<?php
namespace App\Consumer\Stub;

use App\Extractor\ExtractionInterface;
use App\Lib\IdentityInterface;
use DOMElement;

class ExtractorValidPut implements ExtractionInterface, IdentityInterface
{
    private array $data = [];

    private string $id = '1';

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function populate(DOMElement $object): self
    {
        return $this;
    }

    public function extract(): array
    {
        return $this->data;
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
