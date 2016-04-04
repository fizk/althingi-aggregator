<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 11:21 AM
 */

namespace AlthingiAggregator\Model;

use Zend\Hydrator\ExtractionInterface;
use AlthingiAggregator\Model\Exception as ModelException;

class VoteItem implements ExtractionInterface
{

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
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

        if (!$object->getElementsByTagName('atkvæði')->item(0)) {
            throw new ModelException('Missing [{atkvæði}] value', $object);
        }

        return [
            'congressman_id' => (int) $object->getAttribute('id'),
            'vote' => trim($object->getElementsByTagName('atkvæði')->item(0)->nodeValue)
        ];
    }
}
