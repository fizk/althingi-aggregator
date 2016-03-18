<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/06/15
 * Time: 7:19 PM
 */

namespace AlthingiAggregator\Model\Dom;

use \PHPUnit_Framework_TestCase;

class SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \AlthingiAggregator\Model\Exception
     * @throws \AlthingiAggregator\Model\Exception
     */
    public function testInvalidStructure()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');

        $model = new Session();
        $model->extract($root);
    }

    public function testDifferentDateFormat()
    {
        $element = $this->getValidElement();

        $model = new Session();
        $result = $model->extract($element);

        $this->assertEquals('1983-04-23', $result['from']);
        $this->assertEquals('1984-10-09', $result['to']);
    }

    public function testDocumentWithCollection()
    {
        $domDocument = new \DOMDocument();
        $domDocument->load(__DIR__ . '/data/www.althingi.is_altext_xml_thingmenn_thingmadur_thingseta__nr=200.xml');

        $xpath = new \DOMXPath($domDocument);
        $thingsetaElements = $xpath->query('//þingmaður/þingsetur/þingseta');

        foreach ($thingsetaElements as $element) {
            $sessionModel = new Session();
            try {
                $sessionData = $sessionModel->extract($element);
            } catch (\Exception $e) {
                $this->fail($e->getMessage());
            }
        }
    }

    private function getValidElement()
    {
        $xml =
            '<þingsetur>
                <þingseta>
                <þing>106</þing>
                <skammstöfun>GA</skammstöfun>
                <tegund>þingmaður</tegund>
                <þingflokkur id="39">Samtök um kvennalista</þingflokkur>
                <kjördæmi id="14">
                    <![CDATA[Landskjörinn (<110 lt.)]]>
                </kjördæmi>
                <kjördæmanúmer>3</kjördæmanúmer>
                <deild>N</deild>
                <þingsalssæti></þingsalssæti>
                <tímabil>
                    <inn>23.04.1983</inn>
                    <út>09.10.1984</út></tímabil>
                </þingseta>
            </þingsetur>';

        $domDocument = new \DOMDocument();
        $domDocument->loadXML($xml);
        return $domDocument->getElementsByTagName('þingseta')->item(0);
    }
}
