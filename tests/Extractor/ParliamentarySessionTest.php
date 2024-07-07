<?php

namespace App\Extractor;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMXPath;
use App\Extractor\ParliamentarySession;
use App\Extractor\Document;

class ParliamentarySessionTest extends TestCase
{
    public function testValidDocumentAllValues()
    {
        $expectedData = [
            'parliamentary_session_id' => 12,
            'name' => 'þingsetningarfundur',
            'from' => '2011-10-01 11:14',
            'to' => '2011-10-01 11:42',
        ];
        $dom = new DOMDocument();
        $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?>
            <þingfundir>
                <þingfundur númer="12">
                    <fundarheiti>þingsetningarfundur</fundarheiti>
                    <hefst>
                        <texti> 1. október, kl. 11:00 árdegis</texti>
                        <dagur>01.10.2011</dagur>
                        <timi>11:00</timi>
                        <dagurtími>2011-10-01T11:00:00</dagurtími>
                    </hefst>
                    <fundursettur>2011-10-01T11:14:30</fundursettur>
                    <fuslit>2011-10-01T11:42:06</fuslit>
                    <sætaskipan>http://www.althing-01T11:14:30</sætaskipan>
                    <fundarskjöl>
                        <sgml>http://www.althingi.is/altext/140/f000.sgml</sgml>
                        <xml>http://www.althingi.is/xml/140/fundir/fun000.xml</xml>
                    </fundarskjöl>
                    <dagskrá>
                        <xml>http://www.althing&amp;fundur=0</xml>
                    </dagskrá>
                </þingfundur>
            </þingfundir>');
        $documentsXPath = new DOMXPath($dom);
        $documentNodeList = $documentsXPath->query('//þingfundir/þingfundur');

        $documentData = (new ParliamentarySession())
            ->populate($documentNodeList->item(0))
            ->extract();

        $this->assertEquals($expectedData, $documentData);
    }

    public function testValidDocumentRequiredValues()
    {
        $expectedData = [
            'parliamentary_session_id' => 0,
            'name' => '',
            'from' => '',
            'to' => '',
        ];
        $domDocument = new DOMDocument();
        $domDocument->loadXML(
            '<?xml version="1.0" encoding="UTF-8"?>
            <þingfundir>
                <þingfundur númer=\'0\'>
                    <sætaskipan>http://www.althingi.is/</sætaskipan>
                    <fundarskjöl>
                        <sgml>http://www.althingi.is/altext/140/f000.sgml</sgml>
                        <xml>http://www.althingi.is/xml/140/fundir/fun000.xml</xml>
                    </fundarskjöl>
                    <dagskrá>
                        <xml>http://www.althingi.is/altext/</xml>
                    </dagskrá>
                </þingfundur>
            </þingfundir>'
        );
        $documentsXPath = new DOMXPath($domDocument);
        $documentNodeList = $documentsXPath->query('//þingfundir/þingfundur');

        $documentData = (new ParliamentarySession())
            ->populate($documentNodeList->item(0))
            ->extract();

        $this->assertEquals($expectedData, $documentData);
    }

    public function testIdLessThanZero()
    {
        $expectedData = [
            'parliamentary_session_id' => -1,
            'name' => null,
            'from' => null,
            'to' => null,
        ];
        $domDocument = new DOMDocument();
        $domDocument->loadXML(
            '<?xml version="1.0" encoding="UTF-8"?>
            <þingfundir>
                <þingfundur númer="-1">
                    <sætaskipan>http://www.althingi.is/</sætaskipan>
                    <fundarskjöl>
                        <sgml>http://www.althingi.is/altext/140/f000.sgml</sgml>
                        <xml>http://www.althingi.is/xml/140/fundir/fun000.xml</xml>
                    </fundarskjöl>
                    <dagskrá>
                        <xml>http://www.althingi.is/altext/</xml>
                    </dagskrá>
                </þingfundur>
            </þingfundir>'
        );
        $documentsXPath = new DOMXPath($domDocument);
        $documentNodeList = $documentsXPath->query('//þingfundir/þingfundur');

        $documentData = (new ParliamentarySession())
            ->populate($documentNodeList->item(0))
            ->extract();

        $this->assertEquals($expectedData, $documentData);
    }

    public function testInvalidDocument()
    {
        $this->expectException(\App\Extractor\Exception::class);

        $dom = new DOMDocument();
        $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?>
            <þingfundir>
                <þingfundur>
                    <fundarheiti>framhald þingsetningarfundar</fundarheiti>
                    <hefst>
                        <texti> 1. október, kl. 12:30 miðdegis</texti>
                        <dagur>01.10.2011</dagur>
                        <timi>12:30</timi>
                        <dagurtími>2011-10-01T12:30:00</dagurtími>
                    </hefst>
                    <fundursettur>2011-10-01T12:31:16</fundursettur>
                    <sætaskipan>http://www.althi31:16</sætaskipan>
                    <fundarskjöl>
                        <sgml>http://www.althingi.is/altext/140/f001.sgml</sgml>
                        <xml>http://www.althingi.is/xml/140/fundir/fun001.xml</xml>
                    </fundarskjöl>
                    <dagskrá>
                        <xml>http://www.althin=140&amp;fundur=1</xml>
                    </dagskrá>
                </þingfundur>
            </þingfundir>');
        $documentsXPath = new DOMXPath($dom);
        $documentNodeList = $documentsXPath->query('//þingfundir/þingfundur');

        (new Document())
            ->populate($documentNodeList->item(0))
            ->extract();
    }
}
