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

class SpeechTest extends TestCase
{
    public function testValidNewDocument()
    {
        $expectingData = [
            'id' => '20150910T103412',
            'from' => '2015-09-10 10:34:12',
            'to' => '2015-09-10 11:04:48',
            'plenary_id' => 3,
            'assembly_id' => 145,
            'issue_id' => 10,
            'congressman_type' => 'fjármála- og efnahagsráðherra',
            'congressman_id' => 652,
            'iteration' => 1,
            'type' => 'flutningsræða',
            'category' => 'B',
            'text' => '<ræðutexti xmlns="http://skema.althingi.is/skema"><mgr>Herra forseti.</mgr></ræðutexti>',
        ];

        $elements = $this->buildNodeList($this->getValidNewDocument());
        $resultedData = (new Speech())->extract($elements->item(0));

        $this->assertEquals($expectingData, $resultedData);
    }

    public function testValidOldDocument()
    {
        $expectingData = [
            'id' => 'f05685127b922ce5a6a1ad24c10c39a2',
            'from' => '1951-10-02 00:00:00',
            'to' => '1951-10-02 00:00:00',
            'plenary_id' => 1,
            'assembly_id' => 71,
            'issue_id' => 10,
            'congressman_type' => 'forseti alþingis',
            'congressman_id' => 330,
            'iteration' => null,
            'type' => 'útbýting þingskjala',
            'text' => null,
            'category' => 'A',
        ];

        $elements = $this->buildNodeList($this->getValidOldDocument());
        $resultedData = (new Speech())->extract($elements->item(0));

        $this->assertEquals($expectingData, $resultedData);
    }

    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function testInvalidStructure()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');

        $model = new Speech();
        $model->extract($root);
    }

    private function buildNodeList($source)
    {
        $dom = new DOMDocument();
        $dom->loadXML($source);
        $documentsXPath = new DOMXPath($dom);
        return $documentsXPath->query('//ræða');
    }

    private function getValidNewDocument()
    {
        return '<?xml version="1.0"?>
            <ræða fundarnúmer="3" þingnúmer="145" þingmaður="652" þingmál="10">
                <ráðherra>fj&#xE1;rm&#xE1;la- og efnahagsr&#xE1;&#xF0;herra</ráðherra>
                <nafn id="652">Bjarni Benediktsson</nafn>
                <ræðahófst>2015-09-10T10:34:12</ræðahófst>
                <ræðulauk>2015-09-10T11:04:48</ræðulauk>
                <slóðir>
                    <xml>http://www.althingi.is/xml/145/raedur/rad20150910T103412.xml</xml>
                    <html>http://www.althingi.is/altext/raeda/145/rad20150910T103412.html</html>
                </slóðir>
                <tegundræðu>flutningsr&#xE6;&#xF0;a</tegundræðu>
                <umræða>1</umræða>
                <ræðutexti xmlns="http://skema.althingi.is/skema"><mgr>Herra forseti.</mgr></ræðutexti>
                <mál xmlns="http://skema.althingi.is/skema" nr="1" málstegund="l" málsflokkur="B">
                    <málsheiti>fj&#xE1;rl&#xF6;g 2016</málsheiti>
                    <undirtitill/>
                </mál>
            </ræða>';
    }

    private function getValidOldDocument()
    {
        return '<?xml version="1.0"?>
            <ræða fundarnúmer="1" þingmaður="330" þingnúmer="71" þingmál="10">
                <forsetiAlþingis/>
                <nafn id="330">Jón Pálmason</nafn>
                <ræðudagur>02.10.1951</ræðudagur>
                <slóðir/>
                <tegundræðu>útbýting þingskjala</tegundræðu>
                <umræða>-</umræða>
            </ræða>';
    }
}
