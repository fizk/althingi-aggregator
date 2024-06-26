<?php

namespace App\Extractor;

use App\Extractor\CommitteeSitting;
use App\Extractor\MinisterSitting;
use PHPUnit\Framework\TestCase;
use DOMDocument;

class MinisterSittingTest extends TestCase
{
    public function testWithAllData()
    {
        $expectedData = [
            'assembly_id' => 149,
            'ministry_id' => 224,
            'party_id' => 35,
            'from' => '2019-09-06',
            'to' => '2019-09-09',
        ];
        $extractor = new MinisterSitting();

        $element = $this->getDocument()->getElementsByTagName('ráðherraseta')->item(0);

        $resultedData = $extractor->populate($element)->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithNoToDate()
    {
        $expectedData = [
            'assembly_id' => 149,
            'ministry_id' => 224,
            'party_id' => 35,
            'from' => '2019-09-06',
            'to' => null,
        ];
        $extractor = new MinisterSitting();

        $element = $this->getDocument()->getElementsByTagName('ráðherraseta')->item(1);

        $resultedData = $extractor->populate($element)->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithMissingToDate()
    {
        $expectedData = [
            'assembly_id' => 149,
            'ministry_id' => 224,
            'party_id' => 35,
            'from' => '2019-09-06',
            'to' => null,
        ];
        $extractor = new MinisterSitting();

        $element = $this->getDocument()->getElementsByTagName('ráðherraseta')->item(2);

        $resultedData = $extractor->populate($element)->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithMissingParty()
    {
        $expectedData = [
            'assembly_id' => 149,
            'ministry_id' => 224,
            'party_id' => null,
            'from' => '2019-09-06',
            'to' => null,
        ];
        $extractor = new MinisterSitting();

        $element = $this->getDocument()->getElementsByTagName('ráðherraseta')->item(3);

        $resultedData = $extractor->populate($element)->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    private function getDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <root>
                <ráðherraseta>
                    <þing>149</þing>
                    <skammstöfun>ÁslS</skammstöfun>
                    <embætti id="224">dómsmálaráðherra</embætti>
                    <þingflokkur id="35">Sjálfstæðisflokkur</þingflokkur>
                    <tímabil>
                        <inn>06.09.2019</inn>
                        <út>09.09.2019</út>
                    </tímabil>
                </ráðherraseta>
                <ráðherraseta>
                    <þing>149</þing>
                    <skammstöfun>ÁslS</skammstöfun>
                    <embætti id="224">dómsmálaráðherra</embætti>
                    <þingflokkur id="35">Sjálfstæðisflokkur</þingflokkur>
                    <tímabil>
                        <inn>06.09.2019</inn>
                        <út></út>
                    </tímabil>
                </ráðherraseta>
                <ráðherraseta>
                    <þing>149</þing>
                    <skammstöfun>ÁslS</skammstöfun>
                    <embætti id="224">dómsmálaráðherra</embætti>
                    <þingflokkur id="35">Sjálfstæðisflokkur</þingflokkur>
                    <tímabil>
                        <inn>06.09.2019</inn>
                    </tímabil>
                </ráðherraseta>
                <ráðherraseta>
                    <þing>149</þing>
                    <skammstöfun>ÁslS</skammstöfun>
                    <embætti id="224">dómsmálaráðherra</embætti>
                    <tímabil>
                        <inn>06.09.2019</inn>
                    </tímabil>
                </ráðherraseta>
            </root>';
        $dom = new DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
