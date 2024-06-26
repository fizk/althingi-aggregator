<?php

namespace App\Extractor;

use PHPUnit\Framework\TestCase;
use App\Extractor\Assembly;

class AssemblyTest extends TestCase
{
    public function testAllElements()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');
        $root->setAttribute('númer', 1);
        $begin = $dom->createElement('þingsetning');
        $begin->appendChild($dom->createTextNode('10.02.2002'));
        $root->appendChild($begin);

        $end = $dom->createElement('þinglok');
        $end->appendChild($dom->createTextNode('10.02.2002'));
        $root->appendChild($end);

        $dom->appendChild($root);

        $model = new Assembly();
        $result = $model->populate($root)->extract();

        $this->assertEquals(1, $result['no']);
        $this->assertEquals('2002-02-10', $result['from']);
        $this->assertEquals('2002-02-10', $result['to']);
    }

    public function testMissingNumber()
    {
        $this->expectException(\App\Extractor\Exception::class);

        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');
        $begin = $dom->createElement('þingsetning');
        $begin->appendChild($dom->createTextNode('10.02.2002'));
        $root->appendChild($begin);

        $end = $dom->createElement('þinglok');
        $end->appendChild($dom->createTextNode('10.02.2002'));
        $root->appendChild($end);

        $dom->appendChild($root);

        $model = new Assembly();
        $model->populate($root)->extract();
    }

    public function testMissingFromDate()
    {
        $this->expectException(\App\Extractor\Exception::class);

        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');
        $root->setAttribute('númer', 1);

        $end = $dom->createElement('þinglok');
        $end->appendChild($dom->createTextNode('10.02.2002'));
        $root->appendChild($end);

        $dom->appendChild($root);

        $model = new Assembly();
        $model->populate($root)->extract();
    }

    public function testToCanBeNull()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');
        $root->setAttribute('númer', 1);
        $begin = $dom->createElement('þingsetning');
        $begin->appendChild($dom->createTextNode('10.02.2002'));
        $root->appendChild($begin);

        $dom->appendChild($root);

        $model = new Assembly();
        $result = $model->populate($root)->extract();

        $this->assertEquals(1, $result['no']);
        $this->assertEquals('2002-02-10', $result['from']);
        $this->assertNull($result['to']);
    }

    public function testDatesInDifferentFormats()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('whatever');
        $root->setAttribute('númer', 1);
        $begin = $dom->createElement('þingsetning');
        $begin->appendChild($dom->createTextNode('2002-02-10'));
        $root->appendChild($begin);

        $end = $dom->createElement('þinglok');
        $end->appendChild($dom->createTextNode('03/10/2002'));
        $root->appendChild($end);

        $dom->appendChild($root);

        $model = new Assembly();
        $result = $model->populate($root)->extract();

        $this->assertEquals(1, $result['no']);
        $this->assertEquals('2002-02-10', $result['from']);
        $this->assertEquals('2002-03-10', $result['to']);
    }
}
