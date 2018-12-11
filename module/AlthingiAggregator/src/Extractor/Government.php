<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class Government implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array|null
     */
    public function extract(DOMElement $object)
    {
        $id = (new \DateTime($object->getAttribute('from')))->format('Ymd');
        $this->setIdentity($id);

        return [
            'from' => $object->hasAttribute('from') ? $object->getAttribute('from') : null,
            'to' => $object->hasAttribute('to') ? $object->getAttribute('to') : null,
            'title' => trim($object->nodeValue)
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
