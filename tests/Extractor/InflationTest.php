<?php
namespace App\Extractor;

use PHPUnit\Framework\TestCase;
use App\Extractor\Inflation;

class InflationTest extends TestCase
{
    public function testAllElements()
    {

        $dom = new \DOMDocument();
        $element = $dom->createElement('item');
        $element->appendChild($dom->createElement('Date', '2001-01-01'));
        $element->appendChild($dom->createElement('Value', '1.02'));

        $extractor = new Inflation();
        $result = $extractor->extract($element);

        $this->assertEquals('2001-01-01', $result['date']);
        $this->assertEquals(1.02, $result['value']);
        $this->assertEquals('20010101', $extractor->getIdentity());
    }
}
