<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class Constituency implements ExtractionInterface, IdentityInterface
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

    public function setIdentity($id)
    {
        $this->id = $id;
    }

    public function getIdentity()
    {
        return $this->id;
    }
}
