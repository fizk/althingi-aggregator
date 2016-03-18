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

class Speech implements ExtractionInterface, IdentityInterface
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

        if (!$object->hasAttribute('fundarnúmer')) {
            throw new ModelException('Missing [{fundarnúmer}] value', $object);
        }

        if (!$object->hasAttribute('þingnúmer')) {
            throw new ModelException('Missing [{þingnúmer}] value', $object);
        }

        if (!$object->hasAttribute('þingmaður')) {
            throw new ModelException('Missing [{þingmaður}] value', $object);
        }

        if (!$object->getElementsByTagName('ræðahófst')->item(0)) {
            throw new ModelException('Missing [{ræðahófst}] value', $object);
        }

        if (!$object->getElementsByTagName('ræðulauk')->item(0)) {
            throw new ModelException('Missing [{ræðulauk}] value', $object);
        }

        if (!$object->getElementsByTagName('mál')->item(0)) {
            throw new ModelException('Missing [{mál}] value', $object);
        }

        if (!$object->getElementsByTagName('mál')->item(0)->hasAttribute('nr')) {
            throw new ModelException('Missing [{nr}] value', $object);
        }


        $this->setIdentity('r'.$object->getElementsByTagName('ræðahófst')->item(0)->nodeValue);

        $from = date(
            'Y-m-d H:i:s',
            strtotime($object->getElementsByTagName('ræðahófst')->item(0)->nodeValue)
        );
        $to = date(
            'Y-m-d H:i:s',
            strtotime($object->getElementsByTagName('ræðulauk')->item(0)->nodeValue)
        );

        return [
            'id' => $this->getIdentity(),
            'from' => $from,
            'to' => $to,
            'plenary_id' => $object->getAttribute('fundarnúmer'),
            'assembly_id' => $object->getAttribute('þingnúmer'),
            'issue_id' => $object->getElementsByTagName('mál')->item(0)->getAttribute('nr'),
            'congressman_type' => ($object->getElementsByTagName('ráðherra')->item(0))
                ? $object->getElementsByTagName('ráðherra')->item(0)->nodeValue
                : null ,
            'congressman_id' => $object->getAttribute('þingmaður'),
            'iteration' => ($object->getElementsByTagName('umræða')->item(0))
                ? $object->getElementsByTagName('umræða')->item(0)->nodeValue
                : null ,
            'type' => ($object->getElementsByTagName('tegundræðu')->item(0))
                ? $object->getElementsByTagName('tegundræðu')->item(0)->nodeValue
                : null ,
            'text' => ($object->getElementsByTagName('ræðutexti')->item(0))
                ? $object->getElementsByTagName('ræðutexti')->item(0)->nodeValue
                : null ,
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
