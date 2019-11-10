<?php
namespace AlthingiAggregatorTest\Controller;

use AlthingiAggregatorTest\Lib\Consumer\TestConsumer;
use AlthingiAggregatorTest\Lib\Provider\TestProvider;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;
use AlthingiAggregator\Controller;

class CongressmanControllerTest extends AbstractConsoleControllerTestCase
{
    /** @var  TestConsumer */
    private $consumer;

    /** @var  TestProvider */
    private $provider;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../../config/application.config.php'
        );
        parent::setUp();

        $this->provider = new TestProvider();
        $this->consumer = new TestConsumer();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Provider', $this->provider);
        $serviceManager->setService('Consumer', $this->consumer);
    }

    public function testCongressmanRouter()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingmenn',
            $this->getDomDocument()
        );
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingmenn/thingmadur/thingseta/?nr=1021',
            $this->getSessionDocument()
        );
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingmenn/thingmadur/thingseta/?nr=648',
            $this->getSessionDocument()
        );

        $this->dispatch('load:congressman');

        $this->assertControllerName(Controller\CongressmanController::class);
        $this->assertActionName('find-congressman');
    }

    public function testCongressmanConsumerData()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingmenn/?lthing=1',
            $this->getDomDocument()
        );
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingmenn/thingmadur/thingseta/?nr=1021',
            $this->getSessionDocument()
        );
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingmenn/thingmadur/thingseta/?nr=648',
            $this->getSessionDocument()
        );
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingmenn/thingmadur/nefndaseta/?nr=648',
            $this->getCommitteeDocument()
        );
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingmenn/thingmadur/nefndaseta/?nr=1021',
            $this->getCommitteeDocument()
        );

        $this->dispatch('load:congressman --assembly="1"');

        $r = $this->getRequest();
        $consumerStoredData = $this->consumer->getObjects();

        $this->assertCount(6, $consumerStoredData);
        $this->assertArrayHasKey('thingmenn/1021', $consumerStoredData);
        $this->assertArrayHasKey('thingmenn/648', $consumerStoredData);
        $this->assertArrayHasKey('thingmenn/1021/thingseta', $consumerStoredData);
        $this->assertArrayHasKey('thingmenn/648/thingseta', $consumerStoredData);
        $this->assertArrayHasKey('thingmenn/1021/nefndaseta', $consumerStoredData);
        $this->assertArrayHasKey('thingmenn/648/nefndaseta', $consumerStoredData);
    }

    public function getDomDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <þingmannalisti>
            <!-- tilgreina má þingnúmer ?lthing=139 -->
            <!-- eða dagsetningu t.d. ?dagur=14.2.2013 -->
                <þingmaður id=\'1021\'>
                    <nafn>Alma Lísa Jóhannsdóttir</nafn>
                    <fæðingardagur>1972-11-25</fæðingardagur>
                    <skammstöfun>ALJ</skammstöfun>
                    <xml>
                        <nánar>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=1021</nánar>
                        <lífshlaup>http://www.althingi.is/altext/xml/thingmenn/thingmadur/lifshlaup/?nr=1021</lífshlaup>
                        <hagsmunir>http://www.althingi.is/altext/xml/thingmenn/thingmadur/hagsmunir/?nr=1021</hagsmunir>
                        <þingseta>http://www.althingi.is/altext/xml/thingmenn/thingmadur/thingseta/?nr=1021</þingseta>
                        <nefndaseta>
                            http://www.althingi.is/altext/xml/thingmenn/thingmadur/nefndaseta/?nr=1021
                        </nefndaseta>
                    </xml>
                    <html>
                        <lífshlaup>http://www.althingi.is/altext/cv/?nfaerslunr=1021</lífshlaup>
                        <hagsmunir>http://www.althingi.is/altext/hagsmunir/?faerslunr=1021</hagsmunir>
                        <þingstörf>http://www.althingi.is/vefur/thmstorf.html?nfaerslunr=1021</þingstörf>
                    </html>
                </þingmaður>

                <þingmaður id=\'648\'>
                    <nafn>Anna Kristín Gunnarsdóttir</nafn>
                    <fæðingardagur>1952-01-06</fæðingardagur>
                    <skammstöfun>AKG</skammstöfun>
                    <xml>
                        <nánar>http://www.althingi.is/altext/xml/thingmenn/thingmadur/?nr=648</nánar>
                        <lífshlaup>http://www.althingi.is/altext/xml/thingmenn/thingmadur/lifshlaup/?nr=648</lífshlaup>
                        <hagsmunir>http://www.althingi.is/altext/xml/thingmenn/thingmadur/hagsmunir/?nr=648</hagsmunir>
                        <þingseta>http://www.althingi.is/altext/xml/thingmenn/thingmadur/thingseta/?nr=648</þingseta>
                        <nefndaseta>
                            http://www.althingi.is/altext/xml/thingmenn/thingmadur/nefndaseta/?nr=648
                        </nefndaseta>
                    </xml>
                    <html>
                        <lífshlaup>http://www.althingi.is/altext/cv/?nfaerslunr=648</lífshlaup>
                        <hagsmunir>http://www.althingi.is/altext/hagsmunir/?faerslunr=648</hagsmunir>
                        <þingstörf>http://www.althingi.is/vefur/thmstorf.html?nfaerslunr=648</þingstörf>
                    </html>
                </þingmaður>
        </þingmannalisti>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }

    public function getSessionDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <þingmaður id=\'1002\'>
                <nafn>Anna Margrét Guðjónsdóttir</nafn>
                <xml>
                    <lífshlaup>http://www.althingi.is/altext/xml/thingmenn/thingmadur/lifshlaup/?nr=1002</lífshlaup>
                    <hagsmunir>http://www.althingi.is/altext/xml/thingmenn/thingmadur/hagsmunir/?nr=1002</hagsmunir>
                    <þingseta>http://www.althingi.is/altext/xml/thingmenn/thingmadur/thingseta/?nr=1002</þingseta>
                    <nefndaseta>http://www.althingi.is/altext/xml/thingmenn/thingmadur/nefndaseta/?nr=1002</nefndaseta>
                </xml>
                <html>
                    <lífshlaup>http://www.althingi.is/altext/cv/?nfaerslunr=1002</lífshlaup>
                    <hagsmunir>http://www.althingi.is/altext/hagsmunir/?faerslunr=1002</hagsmunir>
                    <þingstörf>http://www.althingi.is/vefur/thmstorf.html?nfaerslunr=1002</þingstörf>
                </html>

                <þingsetur>
                    <þingseta>
                        <þing>138</þing>
                        <skammstöfun>AMG</skammstöfun>
                        <tegund>varamaður</tegund>
                        <þingflokkur id=\'38\'>Samfylkingin</þingflokkur>
                        <kjördæmi id=\'46\'><![CDATA[Suðurkjördæmi]]></kjördæmi>
                        <kjördæmanúmer>5</kjördæmanúmer>
                        <þingsalssæti>30</þingsalssæti>
                        <tímabil>
                        <inn>12.10.2009</inn>
                        <út>26.10.2009</út></tímabil>
                    </þingseta>

                </þingsetur>
            </þingmaður>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }

    public function getCommitteeDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <þingmaður id="1279">
               <nafn>Hildur Sverrisdóttir</nafn>
               <nefndasetur>
                  <nefndaseta>
                     <þing>146</þing>
                     <staða>nefndarmaður</staða>
                     <nefnd id="206">stjórnskipunar- og eftirlitsnefnd</nefnd>
                     <röð>9</röð>
                     <tímabil>
                        <inn>08.02.2017</inn>
                        <út>11.09.2017</út>
                     </tímabil>
                  </nefndaseta>
                  <nefndaseta>
                     <þing>147</þing>
                     <staða>nefndarmaður</staða>
                     <nefnd id="206">stjórnskipunar- og eftirlitsnefnd</nefnd>
                     <röð>9</röð>
                     <tímabil>
                        <inn>12.09.2017</inn>
                        <út>27.10.2017</út>
                     </tímabil>
                  </nefndaseta>
               </nefndasetur>
            </þingmaður>
        ';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
