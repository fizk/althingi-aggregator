<?php
namespace App\Extractor;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use App\Extractor\Proponent;

class ProponentTest extends TestCase
{
    public function testValidDocument()
    {
        $congressmenNodeList = $this->buildNodeList($this->getValidDocument());

        $expectedResults = [
            'congressman_id' => 704,
            'order' => 1,
            'minister' => null
        ];

        $model = (new Proponent())->extract($congressmenNodeList->item(0));

        $this->assertEquals($expectedResults, $model);
    }

    public function testInvalidDocumentNoData()
    {
        $this->expectException(\App\Extractor\Exception::class);

        $congressmenNodeList = $this->buildNodeList($this->getInvalidDocument());

        (new Proponent())
            ->extract($congressmenNodeList->item(0));
    }

    /**
     * @expectedException \App\Extractor\Exception
     * expectedExceptionMessageRegExp '/röð/'
     */
    public function testInvalidDocumentOrderMissing()
    {
        $this->expectException(\App\Extractor\Exception::class);

        $congressmenNodeList = $this->buildNodeList($this->getInvalidDocument());

        (new Proponent())
            ->extract($congressmenNodeList->item(1));
    }



    /**
     * @expectedException \App\Extractor\Exception
     * expectedExceptionMessageRegExp '/id/'
     */
    public function testInvalidDocumentIdMissing()
    {
        $this->expectException(\App\Extractor\Exception::class);

        $congressmenNodeList = $this->buildNodeList($this->getInvalidDocument());

        (new Proponent())
            ->extract($congressmenNodeList->item(2));
    }

    public function testGetDocumentWithMinister()
    {
        $congressmenNodeList = $this->buildNodeList($this->getDocumentWithMinister());
        $proponentData = (new Proponent())
            ->extract($congressmenNodeList->item(0));

        $this->assertEquals('fjármálaráðherra', $proponentData['minister']);
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

    private function getDocumentWithMinister()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <þingskjal>
              <þingskjal skjalsnúmer="1" þingnúmer="135" málsflokkur="A">
                <útbýting>2007-10-01 14:41</útbýting>
                <skjalategund>stjórnarfrumvarp</skjalategund>
                <flutningsmenn>
                  <flutningsmaður röð="1" id="5">
                    <ráðherra>fjármálaráðherra</ráðherra>
                    <nafn>Árni M. Mathiesen</nafn>
                    <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=5</xml>
                  </flutningsmaður>
                </flutningsmenn>
                <slóð>
                  <html>http://www.althingi.is/altext/135/s/0001.html</html>
                  <pdf>http://www.althingi.is/altext/pdf/135/s/0001.pdf</pdf>
                </slóð>
              </þingskjal>
              <málalisti>
                <mál málsnúmer="1" þingnúmer="135">
                  <málsheiti>fjárlög 2008</málsheiti>
                  <html>http://www.althingi.is/dba-bin/ferill.pl?ltg=135&amp;mnr=1</html>
                  <xml>http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=135&amp;malnr=1</xml>
                </mál>
              </málalisti>
            </þingskjal>';
    }
}
