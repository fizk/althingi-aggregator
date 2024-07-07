<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class IssueLink implements ExtractionInterface, IdentityInterface
{
    private DOMElement $object;

    public function populate(DOMElement $object): static
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(): array
    {
        if (! $this->object->hasAttribute('málsnúmer')) {
            throw new Extractor\Exception('Missing [{málsnúmer}] value', $this->object);
        }

        if (! $this->object->hasAttribute('þingnúmer')) {
            throw new Extractor\Exception('Missing [{þingnúmer}] value', $this->object);
        }

        return [
            'to_assembly_id' => (int) $this->object->getAttribute('þingnúmer'),
            'to_issue_id' => (int) $this->object->getAttribute('málsnúmer'),
            'to_kind' => 'A',
            'type' => $this->object->hasAttribute('type')
                ? $this->object->getAttribute('type')
                : null
        ];
    }

    public function setIdentity(string $id): static
    {
        return $this;
    }

    public function getIdentity(): string
    {
        return 'tengdmal';
    }
}
