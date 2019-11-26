<?php
namespace AlthingiAggregatorTest\Controller;

use AlthingiAggregatorTest\Consumer\TestConsumer;
use AlthingiAggregatorTest\Provider\TestProvider;
use AlthingiAggregator\Consumer;
use AlthingiAggregator\Provider;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class PartyControllerTest extends AbstractConsoleControllerTestCase
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

    public function testPartyRouter()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingflokkar',
            $this->getDomDocument()
        );

        $this->dispatch('load:party');

        $this->assertControllerClass('PartyController');
        $this->assertActionName('find-party');
    }

    public function testPartyData()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingflokkar',
            $this->getDomDocument()
        );

        $this->dispatch('load:party');

        $consumerStoredData = $this->consumer->getObjects();

        $this->assertCount(1, $consumerStoredData);
        $this->assertArrayHasKey('thingflokkar/27', $consumerStoredData);
    }

    public function getDomDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <þingflokkar>
              <þingflokkur id="27">
                <heiti>Alþýðuflokkur</heiti>
                <skammstafanir>
                  <stuttskammstöfun>A</stuttskammstöfun>
                  <löngskammstöfun>Alþfl.</löngskammstöfun>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>27</fyrstaþing>
                  <síðastaþing>120</síðastaþing>
                </tímabil>
              </þingflokkur>
            </þingflokkar>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
