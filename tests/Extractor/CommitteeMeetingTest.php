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
}
