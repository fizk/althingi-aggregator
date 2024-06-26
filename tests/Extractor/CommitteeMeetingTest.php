<?php

namespace App\Extractor;

use PHPUnit\Framework\TestCase;
use App\Extractor\CommitteeMeeting;

class CommitteeMeetingTest extends TestCase
{
    public function testWithIssue()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(
            '<?xml version="1.0" ?>
            <nefndarfundur númer="15408" þingnúmer="145">
                <nefnd id="207">fjárlaganefnd</nefnd>
                <hefst>...</hefst>
                <fundursettur>2015-09-14T09:30:00</fundursettur>
                <fuslit>2015-09-14T12:00:00</fuslit>
                <fundargerð>
                    <texti />
                </fundargerð>
                <dagskrá>...</dagskrá>
            </nefndarfundur>
            '
        );

        $expectedResult = [
            'from' => '2015-09-14 09:30:00',
            'to' => '2015-09-14 12:00:00',
            'description' => null
        ];

        $returnedResults = (new CommitteeMeeting())->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedResult, $returnedResults);
    }

    public function testWithSomeText()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(
            '<?xml version="1.0" ?>
            <nefndarfundur númer="15408" þingnúmer="145">
                <nefnd id="207">fjárlaganefnd</nefnd>
                <hefst>...</hefst>
                <fundursettur>2015-09-14T09:30:00</fundursettur>
                <fuslit>2015-09-14T12:00:00</fuslit>
                <fundargerð>
                    <texti>
                    Hundur
                    </texti>
                </fundargerð>
                <dagskrá>...</dagskrá>
            </nefndarfundur>
            '
        );

        $expectedResult = [
            'from' => '2015-09-14 09:30:00',
            'to' => '2015-09-14 12:00:00',
            'description' => 'Hundur'
        ];

        $returnedResults = (new CommitteeMeeting())->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedResult, $returnedResults);
    }
}
