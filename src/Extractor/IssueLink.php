<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class IssueLink implements ExtractionInterface, IdentityInterface
{
    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        if (! $object->hasAttribute('málsnúmer')) {
            throw new Extractor\Exception('Missing [{málsnúmer}] value', $object);
        }

        if (! $object->hasAttribute('þingnúmer')) {
            throw new Extractor\Exception('Missing [{þingnúmer}] value', $object);
        }

        return [
            'assembly_id' => (int) $object->getAttribute('þingnúmer'),
            'issue_id' => (int) $object->getAttribute('málsnúmer'),
            'category' => 'A',
            'type' => $object->hasAttribute('type')
                ? $object->getAttribute('type')
                : null
        ];
    }

    public function setIdentity(string $id): self
    {
        return $this;
    }

    public function getIdentity(): string
    {
        return 'tengdmal';
    }
}
