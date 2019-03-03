<?php
namespace AlthingiAggregatorTest\Controller;

use AlthingiAggregator\Lib\TemporarySpeechDocumentCallback;
use AlthingiAggregatorTest\Lib\Consumer\TestConsumer;
use AlthingiAggregatorTest\Lib\Provider\TestProvider;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;
use Zend\Stdlib\ArrayUtils;

class SpeechControllerTest extends AbstractConsoleControllerTestCase
{
    /** @var  TestConsumer */
    private $consumer;

    /** @var  TestProvider */
    private $provider;

    public function setUp()
    {
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();

        $this->provider = new TestProvider();
        $this->consumer = new TestConsumer();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Provider', $this->provider);
        $serviceManager->setService('Consumer', $this->consumer);
    }

    public function testTemporarySpeechA()
    {
        $this->provider->addDocument(
            'https://www.althingi.is/xml/148/raedur_bradabirgda',
            $this->getSMLDocumentA()
        );
        $this->provider->addDocument(
            'https://www.althingi.is/xml/148/raedur_bradabirgda/rad20171229T130226.xml',
            $this->getDomDocumentA()
        );
        $this->provider->addDocument(
            'https://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=148&malnr=76',
            $this->getIssueDocumentA()
        );

        $this->dispatch('load:tmp-speech --assembly=148');

        $consumerStoredData = $this->consumer->getObjects();

        $this->assertCount(1, $consumerStoredData);
        $this->assertArrayHasKey('loggjafarthing/148/thingmal/76/raedur/20171229T130226', $consumerStoredData);
    }

    public function testTemporarySpeechB()
    {
        $this->provider->addDocument(
            'https://www.althingi.is/xml/148/raedur_bradabirgda',
            $this->getSMLDocumentB()
        );
        $this->provider->addDocument(
            'https://www.althingi.is/xml/148/raedur_bradabirgda/rad20180717T151301.xml',
            $this->getDomDocumentB()
        );
        $this->provider->addDocument(
            'https://www.althingi.is/altext/xml/thingmalalisti/bmal/?lthing=148&malnr=693',
            $this->getIssueDocumentB()
        );

        $this->dispatch('load:tmp-speech --assembly=148');

        $consumerStoredData = $this->consumer->getObjects();

        $this->assertCount(1, $consumerStoredData);
        $this->assertArrayHasKey('loggjafarthing/148/thingmal/693/raedur/20180717T151301', $consumerStoredData);
    }

    public function getSMLDocumentA()
    {
        $cb = new TemporarySpeechDocumentCallback('https://www.althingi.is/xml/148/raedur_bradabirgda');
        return $cb(
            "<A href='rad20171229T130226.xml'>rad20171229T130226.xml</A>
 | <A href='http://hati.althingi.is:8080/fop/fop.pdf/?xmlurl=http://www.althingi.is/xml/148/raedur_bradabirgda/rad20171229T130226.xml&xslurl=http://www.althingi.is/xslt/althingi_raedur_html.xsl&output=xml'>HTML-varpanaþjónn</A> | <a href='http://www.althingi.is/altext/raeda/148/rad20171229T130226.html'>HTML</a> <BR>"
        );
    }

    public function getDomDocumentA()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <?dctm xml_app="Raeduskjal"?>
            <ræða id="r2017-12-29T13:02:26" skst="GÞÞ" embætti="utanrrh." tegr="F" tegflm="R" frhr="0">
              <umsýsla fundur="12" tími="2017-12-29T13:02:26" lgþ="148">
                <mál nr="76" málstegund="a" málsflokkur="A">
                  <málsheiti>samningur</málsheiti>
                  <undirtitill/>
                </mál>
              </umsýsla>
              <ræðutexti>
                <mgr>Virðulegi forseti.</mgr>
              </ræðutexti>
            </ræða>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }

    public function getIssueDocumentA()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <þingmál>
                <mál málsnúmer="76" þingnúmer="148">
                    <málsheiti>samningur milli Íslands og Færeyja um fiskveiðar innan íslenskrar og færeyskrar lögsögu 2017</málsheiti>
                    <efnisgreining/>
                    <málstegund málstegund="a">
                        <heiti>Tillaga til þingsályktunar</heiti>
                        <heiti2>þingsályktunartillaga</heiti2>
                    </málstegund>
                    <staðamáls>Samþykkt sem ályktun Alþingis.</staðamáls>
                    <slóð>
                        <html>http://www.althingi.is/dba-bin/ferill.pl?ltg=148&amp;mnr=76</html>
                        <xml>http://www.althingi.is/altext/xml/thingmalalisti/thingmal/?lthing=148&amp;malnr=76</xml>
                        <rss>http://www.althingi.is/rss/frettir.rss?feed=ferill&amp;malnr=76&amp;lthing=148</rss>
                    </slóð>
                </mál>
                <tengdMál/>
                <efnisflokkar>
                    <yfirflokkur id="1">
                        <heiti>Atvinnuvegir</heiti>
                        <efnisflokkur id="4">
                            <heiti>Sjávarútvegur</heiti>
                            <lýsing>þ.m.t. fiskveiðar, fiskveiðistjórn, fiskvinnsla, hafrannsóknir, hvalveiðar</lýsing>
                            <slóðir>
                                <html>http://www.althingi.is/thingstorf/listar-yfir-mal-a-yfirstandandi-thingi/thingmal-eftir-efnisflokkum/?efnisflokkur=4</html>
                                <xml>http://www.althingi.is/altext/xml/efnisflokkar/efnisflokkur/?efnisflokkur=4</xml>
                            </slóðir>
                        </efnisflokkur>
                    </yfirflokkur>
                    <yfirflokkur id="3">
                        <heiti>Erlend samskipti</heiti>
                        <efnisflokkur id="32">
                            <heiti>Alþjóðasamningar og utanríkismál</heiti>
                            <lýsing>þ.m.t. alþjóðlegir samningar og sáttmálar, alþjóðastofnanir, EES, EFTA, Evrópusambandið, skuldbindingar Íslands á alþjóðavettvangi</lýsing>
                            <slóðir>
                                <html>http://www.althingi.is/thingstorf/listar-yfir-mal-a-yfirstandandi-thingi/thingmal-eftir-efnisflokkum/?efnisflokkur=32</html>
                                <xml>http://www.althingi.is/altext/xml/efnisflokkar/efnisflokkur/?efnisflokkur=32</xml>
                            </slóðir>
                        </efnisflokkur>
                    </yfirflokkur>
                </efnisflokkar>
                <umsagnabeiðnir frestur=""/>
                <erindaskrá/>
                <ræður>
                    <ræða fundarnúmer="12" þingmaður="656" þingnúmer="148">
                        <ráðherra>utanríkisráðherra</ráðherra>
                        <nafn id="656">Guðlaugur Þór Þórðarson</nafn>
                        <ræðahófst>2017-12-29T13:02:26</ræðahófst>
                        <ræðulauk>2017-12-29T13:05:07</ræðulauk>
                        <slóðir/>
                        <tegundræðu>flutningsræða</tegundræðu>
                        <umræða>F</umræða>
                    </ræða>
                    <ræða fundarnúmer="13" þingmaður="1261" þingnúmer="148">
                        <framsögumaðurNefndarálits>Nefnd</framsögumaðurNefndarálits>
                        <nafn id="1261">Áslaug Arna Sigurbjörnsdóttir</nafn>
                        <ræðahófst>2017-12-30T00:19:27</ræðahófst>
                        <ræðulauk>2017-12-30T00:21:13</ræðulauk>
                        <slóðir>
                            <xml>http://www.althingi.is/xml/148/raedur/rad20171230T001927.xml</xml>
                            <html>http://www.althingi.is/altext/raeda/148/rad20171230T001927.html</html>
                        </slóðir>
                        <tegundræðu>ræða</tegundræðu>
                        <umræða>S</umræða>
                    </ræða>
                </ræður>
            </þingmál>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }

    public function getSMLDocumentB()
    {
        $cb = new TemporarySpeechDocumentCallback('https://www.althingi.is/xml/148/raedur_bradabirgda');
        return $cb(
            "<A href='rad20180717T151301.xml'>rad20180717T151301.xml</A>
 | <A href='http://hati.althingi.is:8080/fop/fop.pdf/?xmlurl=http://www.althingi.is/xml/148/raedur_bradabirgda/rad20180717T151301.xml&xslurl=http://www.althingi.is/xslt/althingi_raedur_html.xsl&output=xml'>HTML-varpanaþjónn</A> | <a href='http://www.althingi.is/altext/raeda/148/rad20180717T151301.html'>HTML</a> <BR>"
        );
    }

    public function getDomDocumentB()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <?dctm xml_app="Raeduskjal"?>
            <ræða frhr="0" id="r2018-07-17T15:13:01" skst="SJS" tegflm="F" tegr="R">
                <umsýsla fundur="81" lgþ="148" tími="2018-07-17T15:13:01">
                    <mál málsflokkur="B" málstegund="tk" nr="693">
                        <málsheiti>fundur Alþingis á Þingvöllum</málsheiti>
                    </mál>
                </umsýsla>
                <ræðutexti>
                    <mgr>Forseti</mgr>
                </ræðutexti>
            </ræða>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }

    public function getIssueDocumentB()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <bmál>
                <mál málsflokkur="B" málsnúmer="693" þingnúmer="148">
                    <málsheiti>fundur Alþingis á Þingvöllum</málsheiti>
                    <málstegund málstegund="tk">
                        <heiti>tilkynningar forseta</heiti>
                    </málstegund>
                    <slóð>
                        <html>https://www.althingi.is/thingstorf/thingmalalistar-eftir-thingum/bferill/?ltg=148&amp;mnr=693</html>
                        <xml>http://www.althingi.is/altext/xml/thingmalalisti/bmal/?lthing=148&amp;malnr=693</xml>
                    </slóð>
                </mál>
                <atkvæðagreiðslur/>
                <ræður>
                    <ræða fundarnúmer="81" þingmaður="557" þingnúmer="148">
                        <forsetiAlþingis/>
                        <nafn id="557">Steingrímur J. Sigfússon</nafn>
                        <ræðahófst>2018-07-17T15:13:01</ræðahófst>
                        <ræðulauk>2018-07-17T15:14:09</ræðulauk>
                        <slóðir/>
                        <tegundræðu>ræða</tegundræðu>
                        <umræða>*</umræða>
                    </ræða>
                </ræður>
            </bmál>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
