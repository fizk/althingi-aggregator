<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Category implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        if (! $object->hasAttribute('id')) {
            throw new Extractor\Exception('Missing [{id}] value', $object);
        }

        $this->setIdentity($object->getAttribute('id'));
        $title = ($object->getElementsByTagName('heiti')->item(0))
            ? $object->getElementsByTagName('heiti')->item(0)->nodeValue
            : null;
        $description = ($object->getElementsByTagName('lýsing')->item(0))
            ? $object->getElementsByTagName('lýsing')->item(0)->nodeValue
            : null;

        return [
            'id' => (int) $this->getIdentity(),
            'title' => $title,
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
