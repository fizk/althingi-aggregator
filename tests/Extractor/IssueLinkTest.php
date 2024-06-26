<?php

namespace App\Extractor;

use App\Extractor\IssueLink;
use PHPUnit\Framework\TestCase;
use DOMDocument;

class IssueLinkTest extends TestCase
{
    public function testWithAllData()
    {
        $expectedData = [
            'assembly_id' => 149,
            'issue_id' => 1,
            'kind' => 'A',
            'type' => null,
        ];
        $extractor = new IssueLink();


        $source = '<mál málsnúmer="1" þingnúmer="149"></mál>';
        $dom = new DOMDocument();
        $dom->loadXML($source);

        $resultedData = $extractor->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithType()
    {
        $expectedData = [
            'assembly_id' => 149,
            'issue_id' => 1,
            'kind' => 'A',
            'type' => 'mytype',
        ];
        $extractor = new IssueLink();


        $source = '<mál málsnúmer="1" þingnúmer="149" type="mytype"></mál>';
        $dom = new DOMDocument();
        $dom->loadXML($source);

        $resultedData = $extractor->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithAssemblyMissingData()
    {
        $this->expectException(\App\Extractor\Exception::class);

        $expectedData = [
            'assembly_id' => 149,
            'issue_id' => 1,
            'kind' => 'A',
            'type' => 'mytype',
        ];
        $extractor = new IssueLink();


        $source = '<mál málsnúmer="1" type="mytype"></mál>';
        $dom = new DOMDocument();
        $dom->loadXML($source);

        $resultedData = $extractor->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithIssueMissingData()
    {
        $this->expectException(\App\Extractor\Exception::class);

        $expectedData = [
            'assembly_id' => 149,
            'issue_id' => 1,
            'kind' => 'A',
            'type' => 'mytype',
        ];
        $extractor = new IssueLink();


        $source = '<mál þingnúmer="149" type="mytype"></mál>';
        $dom = new DOMDocument();
        $dom->loadXML($source);

        $resultedData = $extractor->populate($dom->documentElement)->extract();

        $this->assertEquals($expectedData, $resultedData);
    }
}
