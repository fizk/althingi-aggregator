<?php

namespace App\Extractor;

use PHPUnit\Framework\TestCase;
use App\Extractor\Category;

class CategoryTest extends TestCase
{
    public function testAllElements()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('root');
        $root->setAttribute('id', 1);

        $begin = $dom->createElement('heiti');
        $begin->appendChild($dom->createTextNode('some name'));
        $root->appendChild($begin);

        $end = $dom->createElement('lýsing');
        $end->appendChild($dom->createTextNode('some description'));
        $root->appendChild($end);

        $dom->appendChild($root);

        $model = new Category();
        $result = $model->populate($root)->extract();

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('some name', $result['title']);
        $this->assertEquals('some description', $result['description']);
    }

    public function testMissingElements()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('root');
        $root->setAttribute('id', 1);

        $dom->appendChild($root);

        $model = new Category();
        $result = $model->populate($root)->extract();

        $this->assertEquals(1, $result['id']);
        $this->assertEquals(null, $result['title']);
        $this->assertEquals(null, $result['description']);
    }
}
