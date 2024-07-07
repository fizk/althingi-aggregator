<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Issue implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): static
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(): array
    {
//        if (!$object->hasAttribute('málsflokkur')) {
//            throw new Extractor\Exception('Missing [{málsflokkur}] value', $object);
//        }

        if (! $this->object->hasAttribute('málsnúmer')) {
            throw new Extractor\Exception('Missing [{málsnúmer}] value', $this->object);
        }

        if (! $this->object->hasAttribute('þingnúmer')) {
            throw new Extractor\Exception('Missing [{þingnúmer}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('málsheiti')->item(0)) {
            throw new Extractor\Exception('Missing [{málsheiti}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('málstegund')->item(0)) {
            throw new Extractor\Exception('Missing [{málstegund}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('málstegund')->item(0)->hasAttribute('málstegund')) {
            throw new Extractor\Exception('Missing [{málstegund}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('málstegund')->item(0)->getElementsByTagName('heiti')->item(0)) {
            throw new Extractor\Exception('Missing [{heiti}] value', $this->object);
        }

        //----

        $categoriesIds = [];
        $categories = $this->object->getElementsByTagName('efnisflokkar');
        foreach ($categories as $category) {
            $categoriesIds[] = $category->getAttribute('id');
        }

        $this->setIdentity((int) $this->object->getAttribute('málsnúmer'));

        $assemblyId = (int) $this->object->getAttribute('þingnúmer');
        $category = $this->object->hasAttribute('málsflokkur') ? $this->object->getAttribute('málsflokkur') : 'A';
        $status = ($this->object->getElementsByTagName('staðamáls')->length)
            ? trim($this->object->getElementsByTagName('staðamáls')->item(0)->nodeValue)
            : null;
        //@todo currently it seems that the málsheiti and undirheiti have accidentally been switch my the provider.
        $name = $category === 'A'
            ? $this->object->getElementsByTagName('málsheiti')->item(0)->nodeValue
            : $this->object->getElementsByTagName('undirheiti')->item(0)->nodeValue;
        $subName = $this->object->getElementsByTagName('efnisgreining')?->item(0)?->nodeValue;
        $type = ($this->object->getElementsByTagName('málstegund')->length &&
                $this->object->getElementsByTagName('málstegund')->item(0)->hasAttribute('málstegund'))
            ? $this->object->getElementsByTagName('málstegund')->item(0)->getAttribute('málstegund')
            : null;
        $typeName = $this->object->getElementsByTagName('málstegund')
            ?->item(0)?->getElementsByTagName('heiti')?->item(0)?->nodeValue;
        $typeSubName = $this->object->getElementsByTagName('málstegund')
            ?->item(0)?->getElementsByTagName('heiti2')?->item(0)?->nodeValue;
        $congressmanId = $this->object->hasAttribute('framsögumaður')
            ? $this->object->getAttribute('framsögumaður')
            : null;
        $question = $this->object->getElementsByTagName('fyrirspurntil')?->item(0)?->nodeValue;

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
            'kind' => $category,
            'status' => $status,
            'name' => $name,
            'sub_name' => $subName,
            'type' => $type,
            'type_name' => $typeName,
            'type_subname' => $typeSubName,
            'congressman_id' => $congressmanId,
            'question' => $question, //TODO change the name of this
            'goal' => $this->object->getElementsByTagName('markmið')?->item(0)?->nodeValue,
            'major_changes' => $this->object->getElementsByTagName('helstuBreytingar')?->item(0)?->nodeValue,
            'changes_in_law' => $this->object->getElementsByTagName('breytingaráLögum')?->item(0)?->nodeValue,
            'costs_and_revenues' => $this->object->getElementsByTagName('kostnaðurOgTekjur')?->item(0)?->nodeValue,
            'deliveries' => $this->object->getElementsByTagName('afgreiðsla')?->item(0)?->nodeValue,
            'additional_information' => $this->object->getElementsByTagName('aðrarUpplýsingar')?->item(0)?->nodeValue,
        ];
    }

    public function setIdentity(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }
}
