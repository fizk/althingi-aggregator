<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor;

class Category implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array|null
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function extract(DOMElement $object)
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

    public function setIdentity($id)
    {
        $this->id = $id;
    }

    public function getIdentity()
    {
        return $this->id;
    }
}
