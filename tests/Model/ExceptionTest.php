<?php

namespace AlthingiAggregator\Model;

use PHPUnit_Framework_TestCase;
use DOMDocument;

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 4/03/16
 * Time: 9:23 PM
 */
class ExceptionTest extends PHPUnit_Framework_TestCase
{
    private $xml = '<root><stuff><node/></stuff></root>';

    public function testMessage()
    {
        $exception = new Exception('message');

        $this->assertEquals("message", $exception->getMessage());
    }

    public function testDomMessage()
    {
        $doc = new DOMDocument();
        $doc->loadXML($this->xml);

        $exception = new Exception('message', $doc->getElementsByTagName('node')->item(0));

        $this->assertEquals("message\n<node/>", $exception->getMessage());
    }

    public function testPreviousException()
    {
        $exception = new Exception('message', null, new \Exception('previous'));

        $this->assertEquals("message", $exception->getMessage());
    }
}
