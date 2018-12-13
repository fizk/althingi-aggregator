<?php
namespace AlthingiAggregatorTest\Extractor;

use AlthingiAggregator\Extractor\Government;
use PHPUnit\Framework\TestCase;

class GovernmentTest extends TestCase
{
    public function testAllElements()
    {

        $dom = new \DOMDocument();
        $dom->loadXML('
            <root>
                <item from="2001-01-01" to="2001-01-01">title</item>
            </root>
        ');
        $element = $dom->getElementsByTagName('item')->item(0);

        $extractor = new Government();
        $result = $extractor->extract($element);

        $this->assertEquals('2001-01-01', $result['from']);
        $this->assertEquals('2001-01-01', $result['to']);
        $this->assertEquals('title', $result['title']);
        $this->assertEquals('20010101', $extractor->getIdentity());
    }
}
