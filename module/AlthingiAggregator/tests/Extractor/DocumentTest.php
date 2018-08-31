<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/06/15
 * Time: 7:19 PM
 */

namespace AlthingiAggregatorTest\Extractor;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMXPath;

class DocumentTest extends TestCase
{
    public function testValidDocument()
    {
        $expectedData = [
            'issue_id' => 160,
            'assembly_id' => 141,
            'document_id' => 160,
            'type' => 'þáltill.',
            'date' => '2012-09-24 14:49',
            'url' => 'http://www.althingi.is/altext/141/s/0160.html'
        ];
        $documentNodeList = $this->buildNodeList($this->getDocumentWithCommittee());

        $documentData = (new Document())
            ->extract($documentNodeList->item(0));

        $this->assertEquals($expectedData, $documentData);
    }

    public function testMissingType()
    {
        $expectedData = [
            'issue_id' => 160,
            'assembly_id' => 141,
            'document_id' => 160,
            'type' => null,
            'date' => '2012-09-24 14:49',
            'url' => 'http://www.althingi.is/altext/141/s/0160.html'
        ];
        $documentNodeList = $this->buildNodeList($this->getDocumentWithCommittee());

        $documentData = (new Document())
            ->extract($documentNodeList->item(2));

        $this->assertEquals($expectedData, $documentData);
    }

    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     */
    public function testInvalidDocument()
    {
        $documentNodeList = $this->buildNodeList($this->getDocumentWithCommittee());
        (new Document())->extract($documentNodeList->item(1));
    }

    private function buildNodeList($source)
    {
        $dom = new DOMDocument();
        $dom->loadXML($source);
        $documentsXPath = new DOMXPath($dom);
        return $documentsXPath->query('//þingmál/þingskjöl/þingskjal');
    }

    private function getDocumentWithCommittee()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <þingmál>
              <mál málsnúmer="160" þingnúmer="141">
                <málsheiti>yfirfærsla heilsugæslunnar frá ríki til sveitarfélaga</málsheiti>
                <efnisgreining/>
                <málstegund málstegund="a">
                  <heiti>Tillaga til þingsályktunar</heiti>
                  <heiti2>þingsályktunartillaga</heiti2>
                </málstegund>
                <slóð>
                  <html>http://www.althingi.is/dba-bin/ferill.pl?ltg=141&amp;mnr=160</html>
                  <xml>http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=141&amp;malnr=160</xml>
                  <rss>http://www.althingi.is/rss/frettir.rss?feed=ferill&amp;malnr=160&amp;lthing=141</rss>
                </slóð>
              </mál>
              <tengdMál>
            </tengdMál>
              <þingskjöl>
                <þingskjal skjalsnúmer="160" málsnúmer="160" þingnúmer="141">
                  <útbýting>2012-09-24 14:49</útbýting>
                  <skjalategund>þáltill.</skjalategund>
                  <slóð>
                    <html>http://www.althingi.is/altext/141/s/0160.html</html>
                    <pdf>http://www.althingi.is/altext/pdf/141/s/0160.pdf</pdf>
                    <xml>http://www.althingi.is/altext/xml/thingskjol/thingskjal/?lthing=141&amp;skjalnr=160</xml>
                  </slóð>
                </þingskjal>
                <þingskjal>
                  <útbýting>2012-09-24 14:49</útbýting>
                  <skjalategund>þáltill.</skjalategund>
                  <slóð>
                    <html>http://www.althingi.is/altext/141/s/0160.html</html>
                    <pdf>http://www.althingi.is/altext/pdf/141/s/0160.pdf</pdf>
                    <xml>http://www.althingi.is/altext/xml/thingskjol/thingskjal/?lthing=141&amp;skjalnr=160</xml>
                  </slóð>
                </þingskjal>
                <þingskjal skjalsnúmer="160" málsnúmer="160" þingnúmer="141">
                  <útbýting>2012-09-24 14:49</útbýting>
                  <slóð>
                    <html>http://www.althingi.is/altext/141/s/0160.html</html>
                    <pdf>http://www.althingi.is/altext/pdf/141/s/0160.pdf</pdf>
                    <xml>http://www.althingi.is/altext/xml/thingskjol/thingskjal/?lthing=141&amp;skjalnr=160</xml>
                  </slóð>
                </þingskjal>
              </þingskjöl>
              <atkvæðagreiðslur/>
              <umsagnabeiðnir/>
              <erindaskrá/>
              <ræður/>
            </þingmál>';
    }
}
