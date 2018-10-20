<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/05/15
 * Time: 7:22 AM
 */

namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

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
            throw new ModelException('Missing [{id}] value', $object);
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
