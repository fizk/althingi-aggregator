<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/03/2016
 * Time: 1:11 PM
 */

namespace AlthingiAggregator\Model\Dom;

use DOMDocument;
use PHPUnit_Framework_TestCase;

class ProponentTest extends PHPUnit_Framework_TestCase
{
    public function testValidDocument()
    {
        $congressmenNodeList = $this->buildNodeList($this->getValidDocument());

        $expectedResults = [
            'congressman_id' => 704,
            'order' => 1
        ];

        $model = (new Proponent())->extract($congressmenNodeList->item(0));

        $this->assertEquals($expectedResults, $model);
    }

    /**
     * @expectedException \AlthingiAggregator\Model\Exception
     */
    public function testInvalidDocumentNoData()
    {
        $congressmenNodeList = $this->buildNodeList($this->getInvalidDocument());

        (new Proponent())
            ->extract($congressmenNodeList->item(0));
    }

    /**
     * @expectedException \AlthingiAggregator\Model\Exception
     * expectedExceptionMessageRegExp '/röð/'
     */
    public function testInvalidDocumentOrderMissing()
    {
        $congressmenNodeList = $this->buildNodeList($this->getInvalidDocument());

        (new Proponent())
            ->extract($congressmenNodeList->item(1));
    }

    /**
     * @expectedException \AlthingiAggregator\Model\Exception
     * expectedExceptionMessageRegExp '/id/'
     */
    public function testInvalidDocumentIdMissing()
    {
        $congressmenNodeList = $this->buildNodeList($this->getInvalidDocument());

        (new Proponent())
            ->extract($congressmenNodeList->item(2));
    }

    private function buildNodeList($source)
    {
        $dom = new DOMDocument();
        $dom->loadXML($source);
        $documentsXPath = new \DOMXPath($dom);
        return $documentsXPath->query('//þingskjal/þingskjal[1]/flutningsmenn[1]/flutningsmaður');
    }

    private function getValidDocument()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <þingskjal>
                <þingskjal skjalsnúmer="5" þingnúmer="145" málsflokkur="A">
                    <útbýting>2015-09-10 16:52</útbýting>
                    <skjalategund>þáltill.</skjalategund>
                    <uppprentun>1</uppprentun>
                    <flutningsmenn>
                        <flutningsmaður röð="1" id="704">
                            <nafn>Guðmundur Steingrímsson</nafn>
                            <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=704</xml>
                        </flutningsmaður>
                        <flutningsmaður röð="2" id="1158">
                            <nafn>Björt Ólafsdóttir</nafn>
                            <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=1158</xml>
                        </flutningsmaður>
                    </flutningsmenn>
                    <slóð>
                        <html>http://www.althingi.is/altext/145/s/0005.html</html>
                        <pdf>http://www.althingi.is/altext/pdf/145/s/0005.pdf</pdf>
                    </slóð>
                </þingskjal>
                <málalisti>
                    <mál málsnúmer="5" þingnúmer="145">
                        <málsheiti>framtíðargjaldmiðill Íslands</málsheiti>

                    </mál>
                </málalisti>
            </þingskjal>
        ';
    }

    private function getInvalidDocument()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <þingskjal>
                <þingskjal skjalsnúmer="5" þingnúmer="145" málsflokkur="A">
                    <útbýting>2015-09-10 16:52</útbýting>
                    <skjalategund>þáltill.</skjalategund>
                    <uppprentun>1</uppprentun>
                    <flutningsmenn>
                        <flutningsmaður>
                            <nafn>Guðmundur Steingrímsson</nafn>
                            <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=704</xml>
                        </flutningsmaður>
                        <flutningsmaður id="1158">
                            <nafn>Björt Ólafsdóttir</nafn>
                            <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=1158</xml>
                        </flutningsmaður>
                        <flutningsmaður röð="2">
                            <nafn>Björt Ólafsdóttir</nafn>
                            <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=1158</xml>
                        </flutningsmaður>
                    </flutningsmenn>
                    <slóð>
                        <html>http://www.althingi.is/altext/145/s/0005.html</html>
                        <pdf>http://www.althingi.is/altext/pdf/145/s/0005.pdf</pdf>
                    </slóð>
                </þingskjal>
                <málalisti>
                    <mál málsnúmer="5" þingnúmer="145">
                        <málsheiti>framtíðargjaldmiðill Íslands</málsheiti>
                    </mál>
                </málalisti>
            </þingskjal>
        ';
    }
}
