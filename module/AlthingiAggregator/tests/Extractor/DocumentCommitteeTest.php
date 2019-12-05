<?php
namespace AlthingiAggregatorTest\Extractor;

use AlthingiAggregator\Extractor\DocumentCommittee;
use PHPUnit\Framework\TestCase;
use DOMDocument;

class DocumentCommitteeTest extends TestCase
{
    public function testValidDocument()
    {
        $expectedData = [
            'committee_id' => 207,
            'part' => '4. minni hluti',
            'name' => 'fjárlaganefnd',
        ];
        $dom = new DOMDocument();
        $dom->loadXML("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <nefnd id=\"207\">
                   <hluti>4. minni hluti</hluti>
                   <heiti>fjárlaganefnd</heiti>
                   <flutningsmaður id=\"1334\" röð=\"1\">
                      <nafn>Ólafur Ísleifsson</nafn>
                      <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=1334</xml>
                   </flutningsmaður>
                </nefnd>
        ");

        $documentData = (new DocumentCommittee())
            ->extract($dom->firstChild);

        $this->assertEquals($expectedData, $documentData);
    }
    public function testMissingParts()
    {
        $expectedData = [
            'committee_id' => 207,
            'part' => null,
            'name' => null,
        ];
        $dom = new DOMDocument();
        $dom->loadXML("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <nefnd id=\"207\">
                   <flutningsmaður id=\"1334\" röð=\"1\">
                      <nafn>Ólafur Ísleifsson</nafn>
                      <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=1334</xml>
                   </flutningsmaður>
                </nefnd>
        ");

        $documentData = (new DocumentCommittee())
            ->extract($dom->firstChild);

        $this->assertEquals($expectedData, $documentData);
    }

    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     */
    public function testMissingId()
    {
        $dom = new DOMDocument();
        $dom->loadXML("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <nefnd>
                   <hluti>4. minni hluti</hluti>
                   <heiti>fjárlaganefnd</heiti>
                   <flutningsmaður id=\"1334\" röð=\"1\">
                      <nafn>Ólafur Ísleifsson</nafn>
                      <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=1334</xml>
                   </flutningsmaður>
                </nefnd>
        ");

        $documentData = (new DocumentCommittee())
            ->extract($dom->firstChild);
    }
}
