<?php
namespace AlthingiAggregatorTest\Extractor;

use PHPUnit\Framework\TestCase;
use AlthingiAggregator\Extractor\Vote;

class VoteTest extends TestCase
{
    public function testDocumentFull()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents(__DIR__ . '/data/01-atkvaedagreidsla.xml'));

        $voteModel = new Vote();
        $result = $voteModel->extract($dom->documentElement);

        $this->assertInternalType('array', $result);
        $this->assertEquals(52313, $voteModel->getIdentity());
    }

    public function testDocumentIncomplete()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents(__DIR__ . '/data/02-atkvaedagreidsla.xml'));

        $voteModel = new Vote();
        $result = $voteModel->extract($dom->documentElement);

        $this->assertInternalType('array', $result);
        $this->assertEquals(51981, $voteModel->getIdentity());
    }

    public function testToCommittee()
    {
        $dom = new \DOMDocument();
        $dom->loadXML($this->getDocumentWithCommittee());

        $voteModel = new Vote();
        $result = $voteModel->extract($dom->documentElement);

        $this->assertEquals('efnahags- og skattanefnd', $result['committee_to']);
    }

    private function getDocumentWithCommittee()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <atkvæðagreiðsla málsnúmer="4" þingnúmer="135" atkvæðagreiðslunúmer="37240">
              <tími>2007-11-13T18:49:53</tími>
              <tegund>Frv. gengur</tegund>
              <þingskjal skjalsnúmer="4" þingnúmer="135">
                <slóð>
                  <xml>http://www.althingi.is/altext/xml/thingskjol/thingskjal/?lthing=135&amp;skjalnr=4</xml>
                </slóð>
              </þingskjal>
              <nánar><![CDATA[ ]]></nánar>
              <til>efnahags- og skattanefnd</til>
              <niðurstaða>
                <aðferð>yfirlýsing forseta/mál gengur</aðferð>
              </niðurstaða>
            </atkvæðagreiðsla>';
    }
}
