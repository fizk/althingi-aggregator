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

class Issue implements ExtractionInterface, IdentityInterface
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
        if (!$object->hasAttribute('málsnúmer')) {
            throw new ModelException('Missing [{málsnúmer}] value', $object);
        }

        if (!$object->hasAttribute('þingnúmer')) {
            throw new ModelException('Missing [{þingnúmer}] value', $object);
        }

        //if (!$object->hasAttribute('málsflokkur')) {
        //    throw new ModelException('Missing [{málsflokkur}] value');
        //}

        if (!$object->getElementsByTagName('málsheiti')->item(0)) {
            throw new ModelException('Missing [{málsheiti}] value', $object);
        }

        if (!$object->getElementsByTagName('málstegund')->item(0)) {
            throw new ModelException('Missing [{málstegund}] value', $object);
        }

        if (!$object->getElementsByTagName('málstegund')->item(0)->hasAttribute('málstegund')) {
            throw new ModelException('Missing [{málstegund}] value', $object);
        }

        if (!$object->getElementsByTagName('málstegund')->item(0)->getElementsByTagName('heiti')->item(0)) {
            throw new ModelException('Missing [{heiti}] value', $object);
        }

        if (!$object->getElementsByTagName('málstegund')->item(0)->getElementsByTagName('heiti2')->item(0)) {
            throw new ModelException('Missing [{heiti2}] value', $object);
        }

        //----

        $categoriesIds = [];
        $categories = $object->getElementsByTagName('efnisflokkar');
        foreach ($categories as $category) {
            $categoriesIds[] = $category->getAttribute('id');
        }

        $this->setIdentity((int) $object->getAttribute('málsnúmer'));

        $assemblyId = (int) $object->getAttribute('þingnúmer');
        $category = 'A';//$object->getAttribute('málsflokkur'),//TODO
        $status = ($object->getElementsByTagName('staðamáls')->length)
            ? trim($object->getElementsByTagName('staðamáls')->item(0)->nodeValue)
            : null;
        $name = $object->getElementsByTagName('málsheiti')->item(0)->nodeValue;
        $subName = $object->getElementsByTagName('efnisgreining')->item(0)->nodeValue;
        $type = ($object->getElementsByTagName('málstegund')->length &&
                $object->getElementsByTagName('málstegund')->item(0)->hasAttribute('málstegund'))
            ? $object->getElementsByTagName('málstegund')->item(0)->getAttribute('málstegund')
            : null;
        $typeName = $object->getElementsByTagName('málstegund')
            ->item(0)->getElementsByTagName('heiti')->item(0)->nodeValue;
        $typeSubName =  $object->getElementsByTagName('málstegund')
            ->item(0)->getElementsByTagName('heiti2')->item(0)->nodeValue;
        $congressmanId = $object->hasAttribute('framsögumaður')
            ? $object->getAttribute('framsögumaður')
            : null;
        $question = $object->getElementsByTagName('fyrirspurntil')->length
            ? $object->getElementsByTagName('fyrirspurntil')->item(0)->nodeValue
            : null;

//        <tengdMál>
//            <lagtFramÁðurSem>
//              <mál málsnúmer="817" þingnúmer="140">
//                <málsheiti>afturköllun umsóknar Íslands um aðild að Evrópusambandinu</málsheiti>
//                <efnisgreining/>
//                <html>http://www.althingi.is/dba-bin/ferill.pl?ltg=140&amp;mnr=817</html>
//                <xml>http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=140&amp;malnr=817</xml>
//              </mál>
//            </lagtFramÁðurSem>
//          </tengdMál>
        //TODO implement this

        return [
            'id' => $this->getIdentity(),
            'assembly_id' => $assemblyId,
            'category' => $category,
            'status' => $status,
            'name' => $name,
            'sub_name' => $subName,
            'type' => $type,
            'type_name' => $typeName,
            'type_subname' => $typeSubName,
            'congressman_id' => $congressmanId,
            'question' => $question, //TODO change the name of this

            'goal' => $object->getElementsByTagName('markmið')->item(0)->nodeValue,
            'major_changes' => $object->getElementsByTagName('helstuBreytingar')->item(0)->nodeValue,
            'changes_in_law' => $object->getElementsByTagName('breytingaráLögum')->item(0)->nodeValue,
            'costs_and_revenues' => $object->getElementsByTagName('kostnaðurOgTekjur')->item(0)->nodeValue,
            'deliveries' => $object->getElementsByTagName('afgreiðsla')->item(0)->nodeValue,
            'additional_information' => $object->getElementsByTagName('aðrarUpplýsingar')->item(0)->nodeValue,
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
