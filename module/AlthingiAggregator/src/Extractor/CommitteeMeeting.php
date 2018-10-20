<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 5/04/2016
 * Time: 12:31 PM
 */

namespace AlthingiAggregator\Extractor;

use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class CommitteeMeeting implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * @param \DOMElement $object
     * @return array
     * @throws ModelException
     */
    public function extract(\DOMElement $object)
    {
        if (! $object->hasAttribute('númer')) {
            throw new ModelException('Missing [{númer}] value', $object);
        }

        $this->setIdentity((int) $object->getAttribute('númer'));

        $from = ($object->getElementsByTagName('fundursettur')->item(0) &&
            ! empty($object->getElementsByTagName('fundursettur')->item(0)->nodeValue))
            ? date('Y-m-d H:i:s', strtotime($object->getElementsByTagName('fundursettur')->item(0)->nodeValue))
            : null ;

        $to = ($object->getElementsByTagName('fuslit')->item(0) &&
            ! empty($object->getElementsByTagName('fuslit')->item(0)->nodeValue))
            ? date('Y-m-d H:i:s', strtotime($object->getElementsByTagName('fuslit')->item(0)->nodeValue))
            : null ;

        $description = ($object->getElementsByTagName('fundargerð')->item(0) &&
            $object->getElementsByTagName('fundargerð')->item(0)->getElementsByTagName('texti')->item(0))
            ? trim($object->getElementsByTagName('fundargerð')
                ->item(0)->getElementsByTagName('texti')->item(0)->nodeValue)
            : null ;

        return [
            'from' => $from,
            'to' => $to,
            'description' => $description
        ];
    }

    public function setIdentity($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity()
    {
        return $this->id;
    }
}
