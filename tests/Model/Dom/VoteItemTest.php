<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/03/2016
 * Time: 11:23 AM
 */

namespace AlthingiAggregator\Model\Dom;

use PHPUnit_Framework_TestCase;

class VoteItemTest extends PHPUnit_Framework_TestCase
{
    public function testTrue()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents(__DIR__ . '/data/01-atkvaedagreidsla.xml'));
        $congressmanNodeList = $dom->getElementsByTagName('þingmaður');
        $vodeModel = new VoteItem();

        print_r($vodeModel->extract($congressmanNodeList->item(0)));
    }
}
