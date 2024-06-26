<?php

namespace App\Extractor;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMXPath;
use App\Extractor\Committee;

class CommitteeTest extends TestCase
{
    public function testWithAllData()
    {
        $expectedData = [
            'committee_id' => 151,
            'name' => 'allsherjarnefnd',
            'first_assembly_id' => 27,
            'last_assembly_id' => 139,
            'abbr_short' => 'a',
            'abbr_long' => 'allshn.',
        ];
        $extractor = new Committee();

        $element = $this->buildNodeList($this->getDocumentWithCommittee());

        $resultedData = $extractor->populate($element->item(1))->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testMissingEndAssembly()
    {
        $expectedData = [
            'committee_id' => 201,
            'name' => 'allsherjar- og menntamálanefnd',
            'first_assembly_id' => 140,
            'last_assembly_id' => null,
            'abbr_short' => 'am',
            'abbr_long' => 'allsh.- og menntmn.',
        ];
        $extractor = new Committee();

        $element = $this->buildNodeList($this->getDocumentWithCommittee());

        $resultedData = $extractor->populate($element->item(0))->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithInvalidData()
    {
        $this->expectException(\App\Extractor\Exception::class);

        $extractor = new Committee();
        $element = $this->buildNodeList($this->getDocumentWithCommittee());
        $extractor->populate($element->item(2))->extract();
    }

    private function buildNodeList($source)
    {
        $dom = new DOMDocument();
        $dom->loadXML($source);
        $documentsXPath = new DOMXPath($dom);
        return $documentsXPath->query('//nefndir/nefnd');
    }

    private function getDocumentWithCommittee()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <nefndir>
              <nefnd id="201">
                <heiti>allsherjar- og menntamálanefnd</heiti>
                <skammstafanir>
                  <stuttskammstöfun>am</stuttskammstöfun>
                  <löngskammstöfun>allsh.- og menntmn.</löngskammstöfun>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>140</fyrstaþing>
                </tímabil>
              </nefnd>
              <nefnd id="151">
                <heiti>allsherjarnefnd</heiti>
                <skammstafanir>
                  <stuttskammstöfun>a</stuttskammstöfun>
                  <löngskammstöfun>allshn.</löngskammstöfun>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>27</fyrstaþing>
                  <síðastaþing>139</síðastaþing>
                </tímabil>
              </nefnd>
              <nefnd>
                <heiti>atvinnumálanefnd</heiti>
                <skammstafanir>
                  <stuttskammstöfun>atv.</stuttskammstöfun>
                  <löngskammstöfun>atvmn.</löngskammstöfun>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>93</fyrstaþing>
                  <síðastaþing>113</síðastaþing>
                </tímabil>
              </nefnd>
            </nefndir>';
    }
}
