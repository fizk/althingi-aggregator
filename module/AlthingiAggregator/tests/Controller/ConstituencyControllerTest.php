<?php
namespace AlthingiAggregatorTest\Controller;

use AlthingiAggregatorTest\Consumer\TestConsumer;
use AlthingiAggregatorTest\Provider\TestProvider;
use AlthingiAggregator\Consumer;
use AlthingiAggregator\Provider;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class ConstituencyControllerTest extends AbstractConsoleControllerTestCase
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
        $serviceManager->setService(Provider\ProviderInterface::class, $this->provider);
        $serviceManager->setService(Consumer\ConsumerInterface::class, $this->consumer);
    }

//    public function testConstituencyRouter()
//    {
//        $this->provider->addDocument(
//            'http://www.althingi.is/altext/xml/kjordaemi',
//            $this->getDomDocument()
//        );
//
//        $this->dispatch('load:constituency');
//
//        $this->assertControllerClass('ConstituencyController');
//        $this->assertActionName('find-constituency');
//    }

    public function testConstituencyData()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/kjordaemi',
            $this->getDomDocument()
        );

        $this->dispatch('load:constituency');

        $consumerStoredData = $this->consumer->getObjects();

        $this->assertCount(3, $consumerStoredData);
        $this->assertArrayHasKey('kjordaemi/1', $consumerStoredData);
        $this->assertArrayHasKey('kjordaemi/5', $consumerStoredData);
        $this->assertArrayHasKey('kjordaemi/2', $consumerStoredData);
    }

    public function getDomDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <kjördæmin>
              <kjördæmið id="1">
                <heiti><![CDATA[]]></heiti>
                <lýsing><![CDATA[]]></lýsing>
                <skammstafanir>
                  <stuttskammstöfun>-</stuttskammstöfun>
                  <löngskammstöfun/>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>80</fyrstaþing>
                </tímabil>
              </kjördæmið>
              <kjördæmið id="5">
                <heiti><![CDATA[Akureyri]]></heiti>
                <lýsing><![CDATA[]]></lýsing>
                <skammstafanir>
                  <stuttskammstöfun>Ak</stuttskammstöfun>
                  <löngskammstöfun>Ak.</löngskammstöfun>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>19</fyrstaþing>
                  <síðastaþing>79</síðastaþing>
                </tímabil>
              </kjördæmið>
              <kjördæmið id="2">
                <heiti><![CDATA[Austur-Húnavatnssýsla]]></heiti>
                <lýsing><![CDATA[]]></lýsing>
                <skammstafanir>
                  <stuttskammstöfun>AH</stuttskammstöfun>
                  <löngskammstöfun>A.-Húnv.</löngskammstöfun>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>36</fyrstaþing>
                  <síðastaþing>79</síðastaþing>
                </tímabil>
              </kjördæmið>
            </kjördæmin>
            ';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
