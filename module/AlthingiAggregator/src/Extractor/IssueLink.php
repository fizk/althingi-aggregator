<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor;

class IssueLink implements ExtractionInterface, IdentityInterface
{
    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array|null
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function extract(DOMElement $object)
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

    public function setIdentity($id)
    {
    }

    public function getIdentity()
    {
        return 'tengdmal';
    }
}
