<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/05/15
 * Time: 7:22 AM
 */

namespace AlthingiAggregator\Model\Dom;

use AlthingiAggregator\Lib\IdentityInterface;
use Zend\Stdlib\Extractor\ExtractionInterface;
use AlthingiAggregator\Model\Exception as ModelException;

class Constituency implements ExtractionInterface, IdentityInterface
{
    private $id;
    /**
     * Extract values from an object
     *
     * @param  \DOMElement $object
     * @return array|null
     * @throws \AlthingiAggregator\Model\Exception
     */
    public function extract($object)
    {
        if (!$object instanceof \DOMElement) {
            throw new ModelException('Not a valid \DOMElement');
        }

        if (!$object->hasAttribute('id')) {
            throw new ModelException('Missing [{id}] value', $object);
        }

        $this->setIdentity($object->getAttribute('id'));
        $name = $object->getElementsByTagName('heiti')->item(0)->nodeValue;
        $description = $object->getElementsByTagName('lýsing')->item(0)->nodeValue;
        $abbr_short = $object->getElementsByTagName('stuttskammstöfun')->item(0)->nodeValue;
        $abbr_long = $object->getElementsByTagName('löngskammstöfun')->item(0)->nodeValue . PHP_EOL;

        return [
            'id' => (int) $this->getIdentity(),
            'name' => empty($name) ? '-' : trim($name),
            'description' => trim($description),
            'abbr_short' => trim($abbr_short),
            'abbr_long' => trim($abbr_long)
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
