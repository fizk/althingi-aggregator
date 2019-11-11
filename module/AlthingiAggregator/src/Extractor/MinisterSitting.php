<?php
namespace AlthingiAggregator\Extractor;

use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class MinisterSitting implements ExtractionInterface
{
    /**
     * @param \DOMElement $object
     * @return array
     * @throws ModelException
     */
    public function extract(\DOMElement $object)
    {
        /*
         * <ráðherraseta>
            <þing>149</þing>
            <skammstöfun>ÁslS</skammstöfun>
            <embætti id="224">dómsmálaráðherra</embætti>
            <þingflokkur id="35">Sjálfstæðisflokkur</þingflokkur>
            <tímabil>
            <inn>06.09.2019</inn>
            <út>09.09.2019</út>
            </tímabil>
            </ráðherraseta>
         */

        $assembly = $object->getElementsByTagName('þing')->length === 1
            ? $object->getElementsByTagName('þing')->item(0)->nodeValue
            : null;
        if (! $assembly) {
            throw new ModelException('Missing [{þing}] value', $object);
        }

        $ministry = $object->getElementsByTagName('embætti')->length === 1
            ? $object->getElementsByTagName('embætti')->item(0)->getAttribute('id')
            : null;
        if (! $assembly) {
            throw new ModelException('Missing [{embætti}] value', $object);
        }

        $party = $object->getElementsByTagName('þingflokkur')->length === 1
            ? $object->getElementsByTagName('þingflokkur')->item(0)->getAttribute('id')
            : null;

        $from = $object->getElementsByTagName('inn')->length === 1
            ? $object->getElementsByTagName('inn')->item(0)->nodeValue
            : null;
        $to = $object->getElementsByTagName('út')->length === 1
            ? $object->getElementsByTagName('út')->item(0)->nodeValue
            : null ;

        if ($from) {
            $result = [];
            $match = preg_match('/([0-9]{2})\.([0-9]{2})\.([0-9]{4})/', $from, $result);
            $from = $match !== false ? "{$result[3]}-{$result[2]}-{$result[1]}" : null;
        }

        if ($to) {
            $result = [];
            $match = preg_match('/([0-9]{2})\.([0-9]{2})\.([0-9]{4})/', $to, $result);
            $to = $match !== false ? "{$result[3]}-{$result[2]}-{$result[1]}" : null;
        }

        return [
            'assembly_id' => $assembly,
            'ministry_id' => $ministry,
            'party_id' => $party,
            'from' => $from,
            'to' => $to,
        ];
    }
}
