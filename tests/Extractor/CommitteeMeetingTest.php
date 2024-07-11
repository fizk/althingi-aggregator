<?php

namespace App\Extractor;

use PHPUnit\Framework\TestCase;
use App\Extractor\CommitteeMeeting;

class CommitteeMeetingTest extends TestCase
{
    public function testWithNoType()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(
            '<?xml version="1.0" ?>
            <nefndarfundur númer="10766" þingnúmer="140">
                <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                <tegundFundar/>
                <staður>í færeyska herberginu</staður>
                <hefst>
                    <texti>4. október 11, kl. 12:00 á hádegi</texti>
                    <dagur>2011-10-04</dagur>
                    <timi>12:00</timi>
                    <dagurtími>2011-10-04T12:00:00</dagurtími>
                </hefst>
                <nánar>
                    <dagskrá>
                        <xml>http://www.althingi.is/altext/xml/nefndarfundir/nefndarfundur/?dagskrarnumer=10766</xml>
                        <html>http://www.althingi.is/thingnefndir/dagskra-nefndarfunda/?nfaerslunr=10766</html>
                    </dagskrá>
                </nánar>
            </nefndarfundur>
            '
        );

        $expectedResult = [
            'from' => '2011-10-04 12:00:00',
            'to' => null,
            'type' => null,
            'place' => 'í færeyska herberginu',
            'description' => null
        ];

        $returnedResults = (new CommitteeMeeting())->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedResult, $returnedResults);
    }

    public function testWithNoPlace()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(
            '<?xml version="1.0" ?>
            <nefndarfundur númer="10766" þingnúmer="140">
                <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                <tegundFundar/>
                <hefst>
                    <texti>4. október 11, kl. 12:00 á hádegi</texti>
                    <dagur>2011-10-04</dagur>
                    <timi>12:00</timi>
                    <dagurtími>2011-10-04T12:00:00</dagurtími>
                </hefst>
                <nánar>
                    <dagskrá>
                        <xml>http://www.althingi.is/altext/xml/nefndarfundir/nefndarfundur/?dagskrarnumer=10766</xml>
                        <html>http://www.althingi.is/thingnefndir/dagskra-nefndarfunda/?nfaerslunr=10766</html>
                    </dagskrá>
                </nánar>
            </nefndarfundur>
            '
        );

        $expectedResult = [
            'from' => '2011-10-04 12:00:00',
            'to' => null,
            'type' => null,
            'place' => null,
            'description' => null
        ];

        $returnedResults = (new CommitteeMeeting())->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedResult, $returnedResults);
    }

    public function testWithDifferentDate()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(
            '<?xml version="1.0" encoding="UTF-8"?>
            <nefndarfundur númer="15729" þingnúmer="145">
                <nefnd id="207">fjárlaganefnd</nefnd>
                <hefst>
                    <dagur>2015-11-11</dagur>
                    <texti>Strax að loknum þingfundi</texti>
                </hefst>
                <fundursettur>2015-11-11T20:00:00</fundursettur>
                <fuslit>2015-11-11T20:15:00</fuslit>
                <fundargerð>
                    <xml>http://www.althingi.is/altext/xml/nefndarfundir/nefndarfundargerd/?fundarnumer=8010</xml>
                    <xml>http://www.althingi.is/thingnefndir/fastanefndir/fjarlaganefnd/fundargerdir?faerslunr=8010</xml>
                    <texti><![CDATA[text]]></texti>
                </fundargerð>
                <dagskrá>
                    <xml>http://www.althingi.is/altext/xml/dagskra/nefndarfundur/?fundurnumer=15729</xml>
                    <html>http://www.althingi.is/thingnefndir/dagskra-nefndarfunda/?nfaerslunr=15729</html>
                    <dagskrárliðir>
                        <dagskrárliður númer="1">
                            <mál málsnúmer="148" löggjafarþing="145" málsflokkur="A">
                                <html>http://www.althingi.is/thingstorf/thingmalalistar-eftir-thingum/ferill/?ltg=145&amp;mnr=148</html>
                                <xml>http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=145&amp;malnr=148</xml>
                            </mál>
                            <heiti>
                                <![CDATA[opinber fjármál]]>
                            </heiti>
                        </dagskrárliður>
                        <dagskrárliður númer="2">
                            <mál málsnúmer="1509045" málsflokkur="N"></mál>
                            <heiti>
                                <![CDATA[Önnur mál]]>
                            </heiti>
                        </dagskrárliður>
                        <dagskrárliður númer="3">
                            <mál málsnúmer="1509044" málsflokkur="N"></mál>
                            <heiti>
                                <![CDATA[Fundargerð]]>
                            </heiti>
                        </dagskrárliður>
                    </dagskrárliðir>
                </dagskrá>
            </nefndarfundur>
            '
        );

        $expectedResult = [
            'from' => '2015-11-11 20:00:00',
            'to' => '2015-11-11 20:15:00',
            'type' => null,
            'place' => null,
            'description' => 'text'
        ];

        $returnedResults = (new CommitteeMeeting())->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedResult, $returnedResults);
    }

    public function testWithOnlyDayDate()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(
            '<?xml version="1.0" encoding="UTF-8"?>
            <nefndarfundur númer="4139" þingnúmer="132">
                <nefnd id="130">sjávarútvegsnefnd</nefnd>
                <hefst>
                    <dagur>2006-03-03</dagur>
                    <texti>Í hádegishléi</texti>
                </hefst>
                <dagskrá>
                    <xml>http://www.althingi.is/altext/xml/dagskra/nefndarfundur/?fundurnumer=4139</xml>
                    <html>http://www.althingi.is/thingnefndir/dagskra-nefndarfunda/?nfaerslunr=4139</html>
                    <dagskrárliðir>
                        <dagskrárliður númer="1">
                            <mál löggjafarþing="132" málsflokkur="A" málsnúmer="353">
                                <html>http://www.althingi.is/thingstorf/thingmalalistar-eftir-thingum/ferill/?ltg=132mnr=353</html>
                                <xml>http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=132malnr=353</xml>
                            </mál>
                            <heiti>
                                <![CDATA[ stjórn fiskveiða ]]>
                            </heiti>
                        </dagskrárliður>
                        <dagskrárliður númer="2">
                            <mál löggjafarþing="132" málsflokkur="A" málsnúmer="448">
                                <html>http://www.althingi.is/thingstorf/thingmalalistar-eftir-thingum/ferill/?ltg=132mnr=448</html>
                                <xml>http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=132malnr=448</xml>
                            </mál>
                            <heiti>
                                <![CDATA[ stjórn fiskveiða ]]>
                            </heiti>
                        </dagskrárliður>
                        <dagskrárliður númer="3">
                            <heiti>
                                <![CDATA[ Farið yfir umsagnir (ef tími vinnst til). ]]>
                            </heiti>
                        </dagskrárliður>
                        <dagskrárliður númer="4">
                            <heiti>
                                <![CDATA[ Önnur mál. ]]>
                            </heiti>
                        </dagskrárliður>
                    </dagskrárliðir>
                </dagskrá>
            </nefndarfundur>
            '
        );

        $expectedResult = [
            'from' => '2006-03-03 00:00:00',
            'to' => null,
            'type' => null,
            'place' => null,
            'description' => null
        ];

        $returnedResults = (new CommitteeMeeting())->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedResult, $returnedResults);
    }
}
