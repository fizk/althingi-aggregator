<?php

namespace App\Extractor;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMXPath;
use App\Extractor\Constituency;

class ConstituencyTest extends TestCase
{
    public function testMissingId()
    {
        $this->expectException(\App\Extractor\Exception::class);

        $expectedData = [
            'name' => '-',
            'abbr_short' => '-',
            'abbr_long' => '',
            'id' => 1,
            'description' => ''
        ];

        $extractor = new Constituency();

        $element = $this->buildNodeList($this->getRawData());

        $resultedData = $extractor->populate($element->item(0))->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testEverythingAlmostEmpty()
    {
        $expectedData = [
            'name' => '',
            'abbr_short' => '-',
            'abbr_long' => '',
            'id' => 2,
            'description' => ''
        ];

        $extractor = new Constituency();

        $element = $this->buildNodeList($this->getRawData());

        $resultedData = $extractor->populate($element->item(2))->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    private function buildNodeList($source)
    {
        $dom = new DOMDocument();
        $dom->loadXML($source);
        $documentsXPath = new DOMXPath($dom);
        return $documentsXPath->query('//kjördæmin/kjördæmið');
    }

    private function getRawData()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <kjördæmin>
              <kjördæmið>
                <heiti><![CDATA[]]></heiti>
                <lýsing><![CDATA[]]></lýsing>
                <skammstafanir>
                  <stuttskammstöfun>-</stuttskammstöfun>
                  <löngskammstöfun/>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>80</fyrstaþing>
                </tímabil>
              </kjördæmið>
              <kjördæmið id="5">
                <heiti><![CDATA[Akureyri]]></heiti>
                <lýsing><![CDATA[]]></lýsing>
                <skammstafanir>
                  <stuttskammstöfun>Ak</stuttskammstöfun>
                  <löngskammstöfun>Ak.</löngskammstöfun>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>19</fyrstaþing>
                  <síðastaþing>79</síðastaþing>
                </tímabil>
              </kjördæmið>
              <kjördæmið id="2">
                <heiti><![CDATA[]]></heiti>
                <lýsing><![CDATA[]]></lýsing>
                <skammstafanir>
                  <stuttskammstöfun>-</stuttskammstöfun>
                  <löngskammstöfun/>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>80</fyrstaþing>
                </tímabil>
              </kjördæmið>
            </kjördæmin>';
    }
}
