<?php
namespace App\Extractor;

use App\Lib\IdentityInterface;
use DOMElement;
use DateTime;

class Government implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @throws \Exception
     */
    public function extract(DOMElement $object): array
    {
        $id = (new DateTime($object->getAttribute('from')))->format('Ymd');
        $this->setIdentity($id);

        return [
            'from' => $object->hasAttribute('from') ? $object->getAttribute('from') : null,
            'to' => $object->hasAttribute('to') ? $object->getAttribute('to') : null,
            'title' => trim($object->nodeValue)
        ];
    }

    public function setIdentity($id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }
}
