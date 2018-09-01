<?php
namespace AlthingiAggregatorTest\Extractor;

use PHPUnit\Framework\TestCase;
use AlthingiAggregator\Extractor\Session;

class SessionTest extends TestCase
{
    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     * @throws \AlthingiAggregator\Extractor\Exception
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
                $this->assertInternalType('array', $sessionData);
            } catch (\Exception $e) {
                $this->fail($e->getMessage());
            }
        }
    }

    public function testOutDate()
    {
        $domDocument = new \DOMDocument();
        $domDocument->loadXML(
            '<?xml version="1.0" encoding="UTF-8"?>
            <root>
                <þingseta>
                    <þing>145</þing>
                    <skammstöfun>EKG</skammstöfun>
                    <tegund>þingmaður</tegund>
                    <þingflokkur id="35">Sjálfstæðisflokkur</þingflokkur>
                    <kjördæmi id="53">
                        <![CDATA[ Norðvesturkjördæmi ]]>
                    </kjördæmi>
                    <kjördæmanúmer>2</kjördæmanúmer>
                    <þingsalssæti>57</þingsalssæti>
                    <tímabil>
                        <inn>07.04.2016</inn>
                        <út/>
                    </tímabil>
                </þingseta>
            </root>'
        );

        $element = $domDocument->getElementsByTagName('þingseta')->item(0);

        $sessionModel = new Session();
        $sessionData = $sessionModel->extract($element);
        $this->assertNull($sessionData['to']);
        $i = 0;
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
