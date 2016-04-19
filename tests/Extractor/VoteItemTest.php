<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 11:23 AM
 */

namespace AlthingiAggregator\Extractor;

use PHPUnit_Framework_TestCase;
use DOMDocument;
use DOMXPath;

class VoteItemTest extends PHPUnit_Framework_TestCase
{
    public function testValidDocument()
    {
        $congressmenNodeList = $this->buildNodeList($this->getValidDocument());

        $expectedResults = [
            'congressman_id' => 678,
            'vote' => 'greiðir ekki atkvæði'
        ];

        $model = (new VoteItem())->extract($congressmenNodeList->item(0));

        $this->assertEquals($expectedResults, $model);
    }

    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     */
    public function testMissingCongressman()
    {
        $congressmenNodeList = $this->buildNodeList($this->getValidDocument());

        (new VoteItem())
            ->extract($congressmenNodeList->item(1));
    }

    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     */
    public function testMissingResults()
    {
        $congressmenNodeList = $this->buildNodeList($this->getValidDocument());

        (new VoteItem())
            ->extract($congressmenNodeList->item(2));
    }

    private function buildNodeList($source)
    {
        $dom = new DOMDocument();
        $dom->loadXML($source);
        $documentsXPath = new \DOMXPath($dom);
        return $documentsXPath->query('//atkvæðagreiðsla/atkvæðaskrá/þingmaður');
    }

    private function getValidDocument()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <atkvæðagreiðsla atkvæðagreiðslunúmer="52955" málsnúmer="133" þingnúmer="145">
                <tími>2016-03-18T11:08:15</tími>
                <tegund>Frv.</tegund>
                <þingskjal skjalsnúmer="133" þingnúmer="145">
                    <slóð>
                        <xml>http://www.althingi.is/altext/xml/thingskjol/thingskjal/?lthing=145&amp;skjalnr=133</xml>
                    </slóð>
                </þingskjal>
                <nánar><![CDATA[ ]]></nánar>
                <niðurstaða>
                    <aðferð>atkvæðagreiðslukerfi</aðferð>
                    <já>
                        <fjöldi>19</fjöldi>
                    </já>
                    <nei>
                        <fjöldi>0</fjöldi>
                    </nei>
                    <greiðirekkiatkvæði>
                        <fjöldi>17</fjöldi>
                    </greiðirekkiatkvæði>
                    <niðurstaða>samþykkt</niðurstaða>
                </niðurstaða>
                <atkvæðaskrá>
                    <þingmaður id="678">
                        <nafn>Árni Páll Árnason</nafn>
                        <atkvæði>greiðir ekki atkvæði</atkvæði>
                        <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=678</xml>
                    </þingmaður>
                    <þingmaður>
                        <nafn>Ásmundur Einar Daðason</nafn>
                        <atkvæði>fjarverandi</atkvæði>
                        <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=707</xml>
                    </þingmaður>
                    <þingmaður id="678">
                        <nafn>Ásmundur Einar Daðason</nafn>
                        <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=707</xml>
                    </þingmaður>
                </atkvæðaskrá>
            </atkvæðagreiðsla>';
    }
}
