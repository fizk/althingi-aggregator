<?php
namespace AlthingiAggregatorTest\Extractor;

use AlthingiAggregator\Extractor\IssueLink;
use PHPUnit\Framework\TestCase;
use DOMDocument;

class IssueLinkTest extends TestCase
{
    public function testWithAllData()
    {
        $expectedData = [
            'assembly_id' => 149,
            'issue_id' => 1,
            'category' => 'A',
            'type' => null,
        ];
        $extractor = new IssueLink();


        $source = '<mál málsnúmer="1" þingnúmer="149"></mál>';
        $dom = new DOMDocument();
        $dom->loadXML($source);

        $resultedData = $extractor->extract($dom->documentElement);

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithType()
    {
        $expectedData = [
            'assembly_id' => 149,
            'issue_id' => 1,
            'category' => 'A',
            'type' => 'mytype',
        ];
        $extractor = new IssueLink();


        $source = '<mál málsnúmer="1" þingnúmer="149" type="mytype"></mál>';
        $dom = new DOMDocument();
        $dom->loadXML($source);

        $resultedData = $extractor->extract($dom->documentElement);

        $this->assertEquals($expectedData, $resultedData);
    }

    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     */
    public function testWithAssemblyMissingData()
    {
        $expectedData = [
            'assembly_id' => 149,
            'issue_id' => 1,
            'category' => 'A',
            'type' => 'mytype',
        ];
        $extractor = new IssueLink();


        $source = '<mál málsnúmer="1" type="mytype"></mál>';
        $dom = new DOMDocument();
        $dom->loadXML($source);

        $resultedData = $extractor->extract($dom->documentElement);

        $this->assertEquals($expectedData, $resultedData);
    }

    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     */
    public function testWithIssueMissingData()
    {
        $expectedData = [
            'assembly_id' => 149,
            'issue_id' => 1,
            'category' => 'A',
            'type' => 'mytype',
        ];
        $extractor = new IssueLink();


        $source = '<mál þingnúmer="149" type="mytype"></mál>';
        $dom = new DOMDocument();
        $dom->loadXML($source);

        $resultedData = $extractor->extract($dom->documentElement);

        $this->assertEquals($expectedData, $resultedData);
    }
}
