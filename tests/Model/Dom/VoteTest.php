<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 10:35 AM
 */

namespace AlthingiAggregator\Model\Dom;

use PHPUnit_Framework_TestCase;

class VoteTest extends PHPUnit_Framework_TestCase
{
    public function testDocumentFull()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents(__DIR__ . '/data/01-atkvaedagreidsla.xml'));

        $voteModel = new Vote();
        $result = $voteModel->extract($dom->documentElement);

        $this->assertInternalType('array', $result);
        $this->assertEquals(52313, $voteModel->getIdentity());
    }

    public function testDocumentIncomplete()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents(__DIR__ . '/data/02-atkvaedagreidsla.xml'));

        $voteModel = new Vote();
        $result = $voteModel->extract($dom->documentElement);

        $this->assertInternalType('array', $result);
        $this->assertEquals(51981, $voteModel->getIdentity());
    }
}
