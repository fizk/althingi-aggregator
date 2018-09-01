<?php
namespace AlthingiAggregatorTest\Extractor;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMXPath;
use AlthingiAggregator\Extractor\Plenary;
use AlthingiAggregator\Extractor\Document;

class PlenaryTest extends TestCase
{
    public function testValidDocument()
    {
        $expectedData = [
            'plenary_id' => 0,
            'name' => 'þingsetningarfundur',
            'from' => '2011-10-01 11:14',
            'to' => '2011-10-01 11:42',
        ];
        $documentNodeList = $this->buildNodeList($this->getDocument());

        $documentData = (new Plenary())
            ->extract($documentNodeList->item(0));

        $this->assertEquals($expectedData, $documentData);
    }

    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     */
    public function testInvalidDocument()
    {
        $documentNodeList = $this->buildNodeList($this->getDocument());
        (new Document())->extract($documentNodeList->item(1));
    }

    private function buildNodeList($source)
    {
        $dom = new DOMDocument();
        $dom->loadXML($source);
        $documentsXPath = new DOMXPath($dom);
        return $documentsXPath->query('//þingfundir/þingfundur');
    }

    private function getDocument()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <þingfundir>
                <þingfundur númer=\'0\'>
                    <fundarheiti>þingsetningarfundur</fundarheiti>
                    <hefst>
                        <texti> 1. október, kl. 11:00 árdegis</texti>
                        <dagur>01.10.2011</dagur>
                        <timi>11:00</timi>
                        <dagurtími>2011-10-01T11:00:00</dagurtími>
                    </hefst>
                    <fundursettur>2011-10-01T11:14:30</fundursettur>
                    <fuslit>2011-10-01T11:42:06</fuslit>
                    <sætaskipan>http://www.althingi.is/altext/xml/saetaskipan/?timi=2011-10-01T11:14:30</sætaskipan>
                    <fundarskjöl>
                        <sgml>http://www.althingi.is/altext/140/f000.sgml</sgml>
                        <xml>http://www.althingi.is/xml/140/fundir/fun000.xml</xml>
                    </fundarskjöl>
                    <dagskrá>
                        <xml>http://www.althingi.is/altext/xml/dagskra/thingfundur/?lthing=140&amp;fundur=0</xml>
                    </dagskrá>
                    </þingfundur>
                <þingfundur>
                    <fundarheiti>framhald þingsetningarfundar</fundarheiti>
                    <hefst>
                        <texti> 1. október, kl. 12:30 miðdegis</texti>
                        <dagur>01.10.2011</dagur>
                        <timi>12:30</timi>
                        <dagurtími>2011-10-01T12:30:00</dagurtími>
                    </hefst>
                    <fundursettur>2011-10-01T12:31:16</fundursettur>
                    <sætaskipan>http://www.althingi.is/altext/xml/saetaskipan/?timi=2011-10-01T12:31:16</sætaskipan>
                    <fundarskjöl>
                        <sgml>http://www.althingi.is/altext/140/f001.sgml</sgml>
                        <xml>http://www.althingi.is/xml/140/fundir/fun001.xml</xml>
                    </fundarskjöl>
                    <dagskrá>
                        <xml>http://www.althingi.is/altext/xml/dagskra/thingfundur/?lthing=140&amp;fundur=1</xml>
                    </dagskrá>
                </þingfundur>
            </þingfundir>';
    }
}
