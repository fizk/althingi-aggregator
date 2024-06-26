<?php

namespace App\Extractor;

use PHPUnit\Framework\TestCase;
use App\Extractor\CommitteeAgenda;

class CommitteeAgendaTest extends TestCase
{
    public function testWithIssue()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(
            '<?xml version="1.0" ?>
              <dagskrárliður númer="1">
                <mál málsnúmer="1" löggjafarþing="145" málsflokkur="A"></mál>
                <heiti>
                    <![CDATA[ fjárlög 2016 ]]>
                </heiti>
                <Gestir/>
            </dagskrárliður>'
        );

        $expectedResult = [
            'issue_id' => 1,
            'title' => 'fjárlög 2016'
        ];

        $returnedResults = (new CommitteeAgenda())->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedResult, $returnedResults);
    }

    public function testWithOutIssue()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(
            '<?xml version="1.0" ?>
              <dagskrárliður númer="1">
                <heiti>
                    <![CDATA[ fjárlög 2016 ]]>
                </heiti>
                <Gestir/>
            </dagskrárliður>'
        );

        $expectedResult = [
            'issue_id' => null,
            'title' => 'fjárlög 2016'
        ];

        $returnedResults = (new CommitteeAgenda())->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedResult, $returnedResults);
    }
}
