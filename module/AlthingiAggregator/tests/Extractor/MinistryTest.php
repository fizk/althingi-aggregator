<?php
namespace AlthingiAggregatorTest\Extractor;

use AlthingiAggregator\Extractor\CommitteeSitting;
use AlthingiAggregator\Extractor\MinisterSitting;
use AlthingiAggregator\Extractor\Ministry;
use PHPUnit\Framework\TestCase;
use DOMDocument;

class MinistryTest extends TestCase
{
    public function testWithAllData()
    {
        $expectedData = [
            'ministry_id' => 123,
            'name' => 'atvinnumálaráðherra',
            'abbr_short' => 'atvmrh.',
            'abbr_long' => 'atvinnumálarh.',
            'first' => 1,
            'last' => 100,
        ];
        $extractor = new Ministry();

        /** @var  $element |DOMElement */
        $element = $this->getDocument()->getElementsByTagName('ráðherraembætti')->item(0);

        $resultedData = $extractor->extract($element);

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithNoAbbr()
    {
        $expectedData = [
            'ministry_id' => 123,
            'name' => 'atvinnumálaráðherra',
            'abbr_short' => null,
            'abbr_long' => null,
            'first' => 1,
            'last' => 100,
        ];
        $extractor = new Ministry();

        /** @var  $element |DOMElement */
        $element = $this->getDocument()->getElementsByTagName('ráðherraembætti')->item(1);

        $resultedData = $extractor->extract($element);

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithNoLast()
    {
        $expectedData = [
            'ministry_id' => 123,
            'name' => 'atvinnumálaráðherra',
            'abbr_short' => 'atvmrh.',
            'abbr_long' => 'atvinnumálarh.',
            'first' => 1,
            'last' => null,
        ];
        $extractor = new Ministry();

        /** @var  $element |DOMElement */
        $element = $this->getDocument()->getElementsByTagName('ráðherraembætti')->item(2);

        $resultedData = $extractor->extract($element);

        $this->assertEquals($expectedData, $resultedData);
    }

    private function getDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <ráðherrar>
                <ráðherraembætti id="123">
                    <heiti> atvinnumálaráðherra </heiti>
                    <skammstafanir>
                        <stuttskammstöfun>atvmrh.</stuttskammstöfun>
                        <löngskammstöfun>atvinnumálarh.</löngskammstöfun>
                    </skammstafanir>
                    <tímabil>
                        <fyrstaþing>1</fyrstaþing>
                        <síðastaþing>100</síðastaþing>
                    </tímabil>
                </ráðherraembætti>
                <ráðherraembætti id="123">
                    <heiti> atvinnumálaráðherra </heiti>
                    <tímabil>
                        <fyrstaþing>1</fyrstaþing>
                        <síðastaþing>100</síðastaþing>
                    </tímabil>
                </ráðherraembætti>
                <ráðherraembætti id="123">
                    <heiti> atvinnumálaráðherra </heiti>
                    <skammstafanir>
                        <stuttskammstöfun>atvmrh.</stuttskammstöfun>
                        <löngskammstöfun>atvinnumálarh.</löngskammstöfun>
                    </skammstafanir>
                    <tímabil>
                        <fyrstaþing>1</fyrstaþing>
                        <síðastaþing></síðastaþing>
                    </tímabil>
                </ráðherraembætti>
            </ráðherrar>';
        $dom = new DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
