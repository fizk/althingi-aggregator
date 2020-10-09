<?php
namespace App\Consumer\Stub;

use App\Extractor\ExtractionInterface;
use App\Lib\IdentityInterface;

class ExtractorValidPut implements ExtractionInterface, IdentityInterface
{
    private array $data = [];

    private string $id = '1';

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function extract(\DOMElement $object): array
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
