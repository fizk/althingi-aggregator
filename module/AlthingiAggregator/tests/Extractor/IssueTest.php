<?php
namespace AlthingiAggregatorTest\Extractor;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMXPath;
use AlthingiAggregator\Extractor\Issue;

class IssueTest extends TestCase
{
    public function testWithAllData()
    {
        $expectedData =  [
            'id' => 60,
            'assembly_id' => 141,
            'category' => 'A',
            'status' => 'Ekki útrætt á 141. þingi. Bíður 2. umræðu',
            'name' => 'virðisaukaskattur',
            'sub_name' => 'endurgreiðsla skatts vegna kaupa á varmatækjum',
            'type' => 'l',
            'type_name' => 'Frumvarp til laga',
            'type_subname' => 'lagafrumvarp',
            'congressman_id' => 676,
            'question' => 'innanríkisráðherra',
            'goal' => null,
            'major_changes' => null,
            'changes_in_law' => null,
            'costs_and_revenues' => null,
            'deliveries' => null,
            'additional_information' => null,
        ];
        $extractor = new Issue();

        $element = $this->buildNodeList($this->getValidAIssueDocument(), '//þingmál/mál');

        $resultedData = $extractor->extract($element->item(0));

        $this->assertEquals($expectedData, $resultedData);
    }

    public function testWithBData()
    {
        $expectedData =  [
            'id' => 35,
            'assembly_id' => 148,
            'category' => 'B',
            'status' => null,
            'name' => 'óundirbúinn fyrirspurnatími',
            'sub_name' => null,
            'type' => 'ft',
            'type_name' => 'óundirbúinn fyrirspurnatími',
            'type_subname' => null,
            'congressman_id' => null,
            'question' => null,
            'goal' => null,
            'major_changes' => null,
            'changes_in_law' => null,
            'costs_and_revenues' => null,
            'deliveries' => null,
            'additional_information' => null,
        ];
        $extractor = new Issue();

        $element = $this->buildNodeList($this->getValidBIssueDocument(), '//bmál/mál');

        $resultedData = $extractor->extract($element->item(0));

        $this->assertEquals($expectedData, $resultedData);
    }

    private function buildNodeList($source, $query = '//þingmál/mál')
    {
        $dom = new DOMDocument();
        $dom->loadXML($source);
        $documentsXPath = new DOMXPath($dom);
        return $documentsXPath->query($query);
    }

    private function getValidAIssueDocument()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <þingmál>
              <mál málsnúmer="60" þingnúmer="141" framsögumaður="676">
                <málsheiti>virðisaukaskattur</málsheiti>
                <efnisgreining>endurgreiðsla skatts vegna kaupa á varmatækjum</efnisgreining>
                <málstegund málstegund="l">
                  <heiti>Frumvarp til laga</heiti>
                  <heiti2>lagafrumvarp</heiti2>
                </málstegund>
                <fyrirspurntil>innanríkisráðherra</fyrirspurntil>
                <staðamáls>Ekki útrætt á 141. þingi. Bíður 2. umræðu</staðamáls>
                <slóð>
                  <html>http://www.althingi.is/dba-bin/ferill.pl?ltg=141&amp;mnr=60</html>
                  <xml>http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=141&amp;malnr=60</xml>
                  <rss>http://www.althingi.is/rss/frettir.rss?feed=ferill&amp;malnr=60&amp;lthing=141</rss>
                </slóð>
              </mál>
              <tengdMál>
            </tengdMál>
              <framsögumenn>
                <framsögumaður id="676">
                  <nafn>Álfheiður Ingadóttir</nafn>
                  <xml>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=676</xml>
                </framsögumaður>
              </framsögumenn>
              <þingskjöl>
                <þingskjal skjalsnúmer="60" málsnúmer="60" þingnúmer="141">
                  <útbýting>2012-09-14 12:46</útbýting>
                  <skjalategund>frumvarp</skjalategund>
                  <slóð>
                    <html>http://www.althingi.is/altext/141/s/0060.html</html>
                    <pdf>http://www.althingi.is/altext/pdf/141/s/0060.pdf</pdf>
                    <xml>http://www.althingi.is/altext/xml/thingskjol/thingskjal/?lthing=141&amp;skjalnr=60</xml>
                  </slóð>
                </þingskjal>
                <þingskjal skjalsnúmer="1289" málsnúmer="60" þingnúmer="141">
                  <útbýting>2013-03-19 16:57</útbýting>
                  <skjalategund>nál. með brtt.</skjalategund>
                  <slóð>
                    <html>http://www.althingi.is/altext/141/s/1289.html</html>
                    <pdf>http://www.althingi.is/altext/pdf/141/s/1289.pdf</pdf>
                    <xml>http://www.althingi.is/altext/xml/thingskjol/thingskjal/?lthing=141&amp;skjalnr=1289</xml>
                  </slóð>
                </þingskjal>
              </þingskjöl>
              <atkvæðagreiðslur>
                <atkvæðagreiðsla málsnúmer="60" þingnúmer="141" atkvæðagreiðslunúmer="47302">
                  <tími>2012-10-11T16:17:14</tími>
                  <tegund>Frv. gengur til 2. umr.</tegund>
                  <þingskjal skjalsnúmer="60" þingnúmer="141">
                    <slóð>
                      <xml>http://www.althingi.is/altext/xml/thingskjol/thingskjal/?lthing=141&amp;skjalnr=60</xml>
                    </slóð>
                  </þingskjal>
                  <nánar><![CDATA[ ]]></nánar>
                  <samantekt>
                    <aðferð>yfirlýsing forseta/mál gengur</aðferð>
                  </samantekt>
                  <slóð>
                    <html>http://www.althingi.is/dba-bin/atkvgr.pl?nnafnak=47302</html>
                    <xml>http://www.althingi.is/altext/xml/atkvaedagreidslur/atkvaedagreidsla/?numer=47302</xml>
                  </slóð>
                </atkvæðagreiðsla>
                <atkvæðagreiðsla málsnúmer="60" þingnúmer="141" atkvæðagreiðslunúmer="47303">
                  <tími>2012-10-11T16:17:16</tími>
                  <tegund>Frv. gengur</tegund>
                  <þingskjal skjalsnúmer="60" þingnúmer="141">
                    <slóð>
                      <xml>http://www.althingi.is/altext/xml/thingskjol/thingskjal/?lthing=141&amp;skjalnr=60</xml>
                    </slóð>
                  </þingskjal>
                  <nánar><![CDATA[ ]]></nánar>
                  <til id="202">efnahags- og viðskiptanefnd</til>
                  <samantekt>
                    <aðferð>yfirlýsing forseta/mál gengur</aðferð>
                  </samantekt>
                  <slóð>
                    <html>http://www.althingi.is/dba-bin/atkvgr.pl?nnafnak=47303</html>
                    <xml>http://www.althingi.is/altext/xml/atkvaedagreidslur/atkvaedagreidsla/?numer=47303</xml>
                  </slóð>
                </atkvæðagreiðsla>
              </atkvæðagreiðslur>
              <umsagnabeiðnir>
                <umsagnabeiðni málsnúmer="60" þingnúmer="141" umsagnabeiðnanúmer="8160">
                  <dagsetning>2012-10-19</dagsetning>
                  <viðtakandi>Orkusetrið Akureyri</viðtakandi>
                  <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  <umsögn dagbókarnúmer="312"/>
                </umsagnabeiðni>
                <umsagnabeiðni málsnúmer="60" þingnúmer="141" umsagnabeiðnanúmer="8160">
                  <dagsetning>2012-10-19</dagsetning>
                  <viðtakandi>Orkuveita Reykjavíkur</viðtakandi>
                  <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  <umsögn dagbókarnúmer="405"/>
                </umsagnabeiðni>
                <umsagnabeiðni málsnúmer="60" þingnúmer="141" umsagnabeiðnanúmer="8160">
                  <dagsetning>2012-10-19</dagsetning>
                  <viðtakandi>Bændasamtök Íslands</viðtakandi>
                  <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  <umsögn dagbókarnúmer="471"/>
                </umsagnabeiðni>
                <umsagnabeiðni málsnúmer="60" þingnúmer="141" umsagnabeiðnanúmer="8160">
                  <dagsetning>2012-10-19</dagsetning>
                  <viðtakandi>Ríkisskattstjóri</viðtakandi>
                  <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  <umsögn dagbókarnúmer="231"/>
                </umsagnabeiðni>
                <umsagnabeiðni málsnúmer="60" þingnúmer="141" umsagnabeiðnanúmer="8160">
                  <dagsetning>2012-10-19</dagsetning>
                  <viðtakandi>Varmavélar</viðtakandi>
                  <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  <umsögn dagbókarnúmer="368"/>
                </umsagnabeiðni>
                <umsagnabeiðni málsnúmer="60" þingnúmer="141" umsagnabeiðnanúmer="8160">
                  <dagsetning>2012-10-19</dagsetning>
                  <viðtakandi>Samorka, samtök orku- og veitufyrirtækja</viðtakandi>
                  <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  <umsögn dagbókarnúmer="166"/>
                </umsagnabeiðni>
                <umsagnabeiðni málsnúmer="60" þingnúmer="141" umsagnabeiðnanúmer="8160">
                  <dagsetning>2012-10-19</dagsetning>
                  <viðtakandi>Samtök sunnlenskra sveitarfélaga</viðtakandi>
                  <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  <umsögn dagbókarnúmer="413"/>
                </umsagnabeiðni>
              </umsagnabeiðnir>
              <erindaskrá>
                <erindi dagbókarnúmer="166" málsnúmer="60" þingnúmer="141">
                  <sendandi><![CDATA[Samorka, samtök orku- og veitufyrirtækja]]></sendandi>
                  <viðtakandi>
                    <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  </viðtakandi>
                  <tegunderindis tegund="ub">umsögn</tegunderindis>
                  <komudagur>2012-10-19</komudagur>
                  <sendingadagur>2012-10-19</sendingadagur>
                  <slóð>
                    <pdf>http://www.althingi.is/pdf/erindi/?lthing=141&amp;dbnr=166</pdf>
                  </slóð>
                </erindi>
                <erindi dagbókarnúmer="231" málsnúmer="60" þingnúmer="141">
                  <sendandi><![CDATA[Ríkisskattstjóri]]></sendandi>
                  <viðtakandi>
                    <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  </viðtakandi>
                  <tegunderindis tegund="ub">umsögn</tegunderindis>
                  <komudagur>2012-10-26</komudagur>
                  <sendingadagur>2012-10-25</sendingadagur>
                  <slóð>
                    <pdf>http://www.althingi.is/pdf/erindi/?lthing=141&amp;dbnr=231</pdf>
                  </slóð>
                </erindi>
                <erindi dagbókarnúmer="312" málsnúmer="60" þingnúmer="141">
                  <sendandi><![CDATA[Orkusetrið Akureyri]]></sendandi>
                  <viðtakandi>
                    <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  </viðtakandi>
                  <tegunderindis tegund="ub">umsögn</tegunderindis>
                  <komudagur>2012-11-02</komudagur>
                  <sendingadagur>2012-10-31</sendingadagur>
                  <slóð>
                    <pdf>http://www.althingi.is/pdf/erindi/?lthing=141&amp;dbnr=312</pdf>
                  </slóð>
                </erindi>
                <erindi dagbókarnúmer="366" málsnúmer="60" þingnúmer="141">
                  <sendandi><![CDATA[Hjálmar Jóhannesson og Jeff Clemmensen]]></sendandi>
                  <viðtakandi>
                    <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  </viðtakandi>
                  <tegunderindis tegund="ub">umsögn</tegunderindis>
                  <komudagur>2012-11-05</komudagur>
                  <sendingadagur>2012-11-05</sendingadagur>
                  <slóð>
                    <pdf>http://www.althingi.is/pdf/erindi/?lthing=141&amp;dbnr=366</pdf>
                  </slóð>
                </erindi>
                <erindi dagbókarnúmer="368" málsnúmer="60" þingnúmer="141">
                  <sendandi><![CDATA[Varmavélar]]></sendandi>
                  <viðtakandi>
                    <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  </viðtakandi>
                  <tegunderindis tegund="ub">umsögn</tegunderindis>
                  <komudagur>2012-11-06</komudagur>
                  <sendingadagur>2012-11-06</sendingadagur>
                  <slóð>
                    <pdf>http://www.althingi.is/pdf/erindi/?lthing=141&amp;dbnr=368</pdf>
                  </slóð>
                </erindi>
                <erindi dagbókarnúmer="405" málsnúmer="60" þingnúmer="141">
                  <sendandi><![CDATA[Orkuveita Reykjavíkur]]></sendandi>
                  <viðtakandi>
                    <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  </viðtakandi>
                  <tegunderindis tegund="ub">umsögn</tegunderindis>
                  <komudagur>2012-11-07</komudagur>
                  <sendingadagur>2012-11-07</sendingadagur>
                  <slóð>
                    <pdf>http://www.althingi.is/pdf/erindi/?lthing=141&amp;dbnr=405</pdf>
                  </slóð>
                </erindi>
                <erindi dagbókarnúmer="413" málsnúmer="60" þingnúmer="141">
                  <sendandi><![CDATA[Samtök sunnlenskra sveitarfélaga]]></sendandi>
                  <viðtakandi>
                    <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  </viðtakandi>
                  <tegunderindis tegund="ub">umsögn</tegunderindis>
                  <komudagur>2012-11-08</komudagur>
                  <sendingadagur>2012-11-06</sendingadagur>
                  <slóð>
                    <pdf>http://www.althingi.is/pdf/erindi/?lthing=141&amp;dbnr=413</pdf>
                  </slóð>
                </erindi>
                <erindi dagbókarnúmer="471" málsnúmer="60" þingnúmer="141">
                  <sendandi><![CDATA[Bændasamtök Íslands]]></sendandi>
                  <viðtakandi>
                    <nefnd id="202">efnahags- og viðskiptanefnd</nefnd>
                  </viðtakandi>
                  <tegunderindis tegund="ub">umsögn</tegunderindis>
                  <komudagur>2012-11-13</komudagur>
                  <sendingadagur>2012-11-13</sendingadagur>
                  <slóð>
                    <pdf>http://www.althingi.is/pdf/erindi/?lthing=141&amp;dbnr=471</pdf>
                  </slóð>
                </erindi>
              </erindaskrá>
              <ræður>
                <ræða fundarnúmer="18" þingnúmer="141" þingmaður="124">
                  <nafn id="124">Einar K. Guðfinnsson</nafn>
                  <ræðahófst>2012-10-11T15:39:40</ræðahófst>
                  <ræðulauk>2012-10-11T15:50:41</ræðulauk>
                  <slóðir>
                    <xml>http://www.althingi.is/xml/141/raedur/rad20121011T153940.xml</xml>
                    <html>http://www.althingi.is/altext/raeda/141/rad20121011T153940.html</html>
                  </slóðir>
                  <tegundræðu>flutningsræða</tegundræðu>
                  <umræða>1</umræða>
                </ræða>
                <ræða fundarnúmer="18" þingnúmer="141" þingmaður="396">
                  <nafn id="396">Kristján L. Möller</nafn>
                  <ræðahófst>2012-10-11T15:50:49</ræðahófst>
                  <ræðulauk>2012-10-11T16:02:25</ræðulauk>
                  <slóðir>
                    <xml>http://www.althingi.is/xml/141/raedur/rad20121011T155049.xml</xml>
                    <html>http://www.althingi.is/altext/raeda/141/rad20121011T155049.html</html>
                  </slóðir>
                  <tegundræðu>ræða</tegundræðu>
                  <umræða>1</umræða>
                </ræða>
                <ræða fundarnúmer="18" þingnúmer="141" þingmaður="687">
                  <nafn id="687">Illugi Gunnarsson</nafn>
                  <ræðahófst>2012-10-11T16:02:33</ræðahófst>
                  <ræðulauk>2012-10-11T16:11:04</ræðulauk>
                  <slóðir>
                    <xml>http://www.althingi.is/xml/141/raedur/rad20121011T160233.xml</xml>
                    <html>http://www.althingi.is/altext/raeda/141/rad20121011T160233.html</html>
                  </slóðir>
                  <tegundræðu>ræða</tegundræðu>
                  <umræða>1</umræða>
                </ræða>
                <ræða fundarnúmer="18" þingnúmer="141" þingmaður="124">
                  <nafn id="124">Einar K. Guðfinnsson</nafn>
                  <ræðahófst>2012-10-11T16:11:13</ræðahófst>
                  <ræðulauk>2012-10-11T16:14:02</ræðulauk>
                  <slóðir>
                    <xml>http://www.althingi.is/xml/141/raedur/rad20121011T161113.xml</xml>
                    <html>http://www.althingi.is/altext/raeda/141/rad20121011T161113.html</html>
                  </slóðir>
                  <tegundræðu>ræða</tegundræðu>
                  <umræða>1</umræða>
                </ræða>
                <ræða fundarnúmer="18" þingnúmer="141" þingmaður="396">
                  <nafn id="396">Kristján L. Möller</nafn>
                  <ræðahófst>2012-10-11T16:14:09</ræðahófst>
                  <ræðulauk>2012-10-11T16:17:10</ræðulauk>
                  <slóðir>
                    <xml>http://www.althingi.is/xml/141/raedur/rad20121011T161409.xml</xml>
                    <html>http://www.althingi.is/altext/raeda/141/rad20121011T161409.html</html>
                  </slóðir>
                  <tegundræðu>ræða</tegundræðu>
                  <umræða>1</umræða>
                </ræða>
              </ræður>
            </þingmál>';
    }

    private function getValidBIssueDocument() {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <bmál>
                <mál málsnúmer=\'35\' þingnúmer=\'148\' málsflokkur=\'B\'>
                    <málsheiti>óundirbúinn fyrirspurnatími</málsheiti>
                    <undirheiti>óundirbúinn fyrirspurnatími</undirheiti>
                    <málstegund málstegund=\'ft\'>
                    <heiti>óundirbúinn fyrirspurnatími</heiti></málstegund>
                    <slóð>
                        <html>https://www.althingi.is/thingstorf/thingmalalistar-eftir-thingum/bferill/?ltg=148&amp;mnr=35</html>
                        <xml>http://www.althingi.is/altext/xml/thingmalalisti/bmal/?lthing=148&amp;malnr=35</xml>
                    </slóð>
                </mál>
                <atkvæðagreiðslur></atkvæðagreiðslur>
                <ræður>
                    <ræða fundarnúmer=\'5\' þingnúmer=\'148\'  þingmaður=\'557\'>
                        <forsetiAlþingis></forsetiAlþingis>
                        <nafn id=\'557\'>Steingrímur J. Sigfússon</nafn>
                        <ræðahófst>2017-12-19T13:33:04</ræðahófst>
                        <ræðulauk>2017-12-19T13:33:05</ræðulauk>
                        <slóðir>
                            <xml>http://www.althingi.is/xml/148/raedur/rad20171219T133304.xml</xml>
                            <html>http://www.althingi.is/altext/raeda/148/rad20171219T133304.html</html>
                         </slóðir>
                         <tegundræðu>ræða</tegundræðu>
                        <umræða>*</umræða>
                    </ræða>
                </ræður>
            </bmál>
        ';
    }
}
