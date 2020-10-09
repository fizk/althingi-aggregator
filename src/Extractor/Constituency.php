<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Constituency implements ExtractionInterface, IdentityInterface
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

        $this->setIdentity((int) $object->getAttribute('id'));

        $name = ($object->getElementsByTagName('heiti')->length)
            ? trim($object->getElementsByTagName('heiti')->item(0)->nodeValue)
            : null;
        $description = ($object->getElementsByTagName('lýsing')->length)
            ? trim($object->getElementsByTagName('lýsing')->item(0)->nodeValue)
            : null;
        $abbr_short = ($object->getElementsByTagName('stuttskammstöfun')->length)
            ? trim($object->getElementsByTagName('stuttskammstöfun')->item(0)->nodeValue)
            : null;
        $abbr_long = ($object->getElementsByTagName('löngskammstöfun')->length)
            ? trim($object->getElementsByTagName('löngskammstöfun')->item(0)->nodeValue)
            : null ;

        return [
            'id' => $this->getIdentity(),
            'name' => $name,
            'description' => $description,
            'abbr_short' => $abbr_short,
            'abbr_long' => $abbr_long
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
