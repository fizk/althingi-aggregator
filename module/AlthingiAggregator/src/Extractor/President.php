<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/05/15
 * Time: 7:22 AM
 */

namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Extractor\Exception as ModelException;

class President implements ExtractionInterface
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
        if (!$object->hasAttribute('id')) {
            throw new ModelException('Missing [{id}] value', $object);
        }

        if (!$object->getElementsByTagName('nafn')->item(0)) {
            throw new ModelException('Missing [{nafn}] value', $object);
        }

        if (!$object->getElementsByTagName('þing')->item(0)) {
            throw new ModelException('Missing [{þing}] value', $object);
        }

        if (!$object->getElementsByTagName('inn')->item(0)) {
            throw new ModelException('Missing [{þing}] value', $object);
        }

        $from = date(
            'Y-m-d',
            strtotime($object->getElementsByTagName('inn')->item(0)->nodeValue)
        );
        $to = ($object->getElementsByTagName('út')->item(0))
            ? date('Y-m-d', strtotime($object->getElementsByTagName('út')->item(0)->nodeValue))
            : null ;

        return [
            'assembly_id' => (int) $object->getElementsByTagName('þing')->item(0)->nodeValue,
            'congressman_id' => (int) $object->getAttribute('id'),
            'title' => $object->getElementsByTagName('embættisheiti')->item(0)
                ? $object->getElementsByTagName('embættisheiti')->item(0)->nodeValue
                : '',
            'attr' => $object->getElementsByTagName('skammstöfun')->item(0)
                ? $object->getElementsByTagName('skammstöfun')->item(0)->nodeValue
                : '',
            'from' => $from,
            'to' => $to
        ];
    }
}