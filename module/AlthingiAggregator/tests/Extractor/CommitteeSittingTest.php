<?php
namespace AlthingiAggregatorTest\Extractor;

use AlthingiAggregator\Extractor\CommitteeSitting;
use PHPUnit\Framework\TestCase;
use DOMDocument;

class CommitteeSittingTest extends TestCase
{
    public function testWithAllData()
    {
        $expectedData = [
            'assembly_id' => 146,
            'committee_id' => 206,
            'role' => 'nefndarmaður',
            'order' => 9,
            'from' => '2017-02-08',
            'to' => '2017-09-11',
        ];
        $extractor = new CommitteeSitting();

        /** @var  $element |DOMElement */
        $element = $this->getDocument()->getElementsByTagName('nefndaseta')->item(0);

        $resultedData = $extractor->extract($element);

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithNoToDate()
    {
        $expectedData = [
            'assembly_id' => 146,
            'committee_id' => 206,
            'role' => 'nefndarmaður',
            'order' => 9,
            'from' => '2017-02-08',
            'to' => null,
        ];
        $extractor = new CommitteeSitting();

        /** @var  $element |DOMElement */
        $element = $this->getDocument()->getElementsByTagName('nefndaseta')->item(1);

        $resultedData = $extractor->extract($element);

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithMissingToDate()
    {
        $expectedData = [
            'assembly_id' => 146,
            'committee_id' => 206,
            'role' => 'nefndarmaður',
            'order' => 9,
            'from' => '2017-02-08',
            'to' => null,
        ];
        $extractor = new CommitteeSitting();

        /** @var  $element |DOMElement */
        $element = $this->getDocument()->getElementsByTagName('nefndaseta')->item(1);

        $resultedData = $extractor->extract($element);

        $this->assertEquals($expectedData, $resultedData);
    }

    private function getDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <root>
                <nefndaseta>
                    <þing>146</þing>
                    <staða>nefndarmaður</staða>
                    <nefnd id="206">stjórnskipunar- og eftirlitsnefnd</nefnd>
                    <röð>9</röð>
                    <tímabil>
                    <inn>08.02.2017</inn>
                    <út>11.09.2017</út>
                    </tímabil>
                </nefndaseta>
                <nefndaseta>
                    <þing>146</þing>
                    <staða>nefndarmaður</staða>
                    <nefnd id="206">stjórnskipunar- og eftirlitsnefnd</nefnd>
                    <röð>9</röð>
                    <tímabil>
                    <inn>08.02.2017</inn>
                    <út></út>
                    </tímabil>
                </nefndaseta>
                <nefndaseta>
                    <þing>146</þing>
                    <staða>nefndarmaður</staða>
                    <nefnd id="206">stjórnskipunar- og eftirlitsnefnd</nefnd>
                    <röð>9</röð>
                    <tímabil>
                    <inn>08.02.2017</inn>
                    </tímabil>
                </nefndaseta>
            </root>';
        $dom = new DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
