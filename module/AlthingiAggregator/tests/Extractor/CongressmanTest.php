<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/06/15
 * Time: 7:19 PM
 */

namespace AlthingiAggregatorTest\Extractor;

use PHPUnit\Framework\TestCase;

class CongressmanTest extends TestCase
{
    public function testAllElements()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');
        $root->setAttribute('id', 1);
        $name = $dom->createElement('nafn');
        $name->appendChild($dom->createTextNode('Hundur'));

        $birth = $dom->createElement('fæðingardagur');
        $birth->appendChild($dom->createTextNode('2000-01-01'));

        $root->appendChild($name);
        $root->appendChild($birth);

        $dom->appendChild($root);

        $model = new Congressman();
        $result = $model->extract($root);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Hundur', $result['name']);
        $this->assertEquals('2000-01-01', $result['birth']);
    }

    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function testMissingNumber()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');

        $name = $dom->createElement('nafn');
        $name->appendChild($dom->createTextNode('Hundur'));

        $birth = $dom->createElement('fæðingardagur');
        $birth->appendChild($dom->createTextNode('2000-01-01'));

        $root->appendChild($name);
        $root->appendChild($birth);

        $dom->appendChild($root);

        $model = new Congressman();
        $result = $model->extract($root);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Hundur', $result['name']);
        $this->assertEquals('2000-01-01', $result['birth']);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testNotValidDomElement()
    {
        $root = new \stdClass();
        $model = new Congressman();
        $model->extract($root);
    }

    public function testDatesInDifferentFormats()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');
        $root->setAttribute('id', 1);
        $name = $dom->createElement('nafn');
        $name->appendChild($dom->createTextNode('Hundur'));

        $birth = $dom->createElement('fæðingardagur');
        $birth->appendChild($dom->createTextNode('01.01.2000'));

        $root->appendChild($name);
        $root->appendChild($birth);

        $dom->appendChild($root);

        $model = new Congressman();
        $result = $model->extract($root);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Hundur', $result['name']);
        $this->assertEquals('2000-01-01', $result['birth']);
    }

    /**
     * @expectedException \AlthingiAggregator\Extractor\Exception
     * @throws \AlthingiAggregator\Extractor\Exception
     */
    public function testMissingName()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');
        $root->setAttribute('id', 1);

        $birth = $dom->createElement('fæðingardagur');
        $birth->appendChild($dom->createTextNode('2000-01-01'));

        $root->appendChild($birth);

        $dom->appendChild($root);

        $model = new Congressman();
        $result = $model->extract($root);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Hundur', $result['name']);
        $this->assertEquals('2000-01-01', $result['birth']);
    }
}
