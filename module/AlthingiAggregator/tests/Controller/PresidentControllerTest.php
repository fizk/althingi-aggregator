<?php
namespace AlthingiAggregatorTest\Controller;

use AlthingiAggregatorTest\Consumer\TestConsumer;
use AlthingiAggregatorTest\Provider\TestProvider;
use AlthingiAggregator\Consumer;
use AlthingiAggregator\Provider;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class PresidentControllerTest extends AbstractConsoleControllerTestCase
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

    public function testFindByAssembly()
    {
        $this->provider->addDocument(
            'http://huginn.althingi.is/altext/xml/forsetar/',
            $this->getDomDocument()
        );

        $this->dispatch('load:president --assembly=2');

        $this->assertCount(2, $this->consumer);

        $this->assertControllerClass('PresidentController');
        $this->assertActionName('find-president');
    }

    public function testFindAll()
    {
        $this->provider->addDocument(
            'http://huginn.althingi.is/altext/xml/forsetar/',
            $this->getDomDocument()
        );

        $this->dispatch('load:president');

        $this->assertCount(4, $this->consumer);

        $this->assertControllerClass('PresidentController');
        $this->assertActionName('find-president');
    }


    public function getDomDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <forsetalisti>
                <forseti id="1">
                    <þing>1</þing>
                    <nafn>Jón Jacobson</nafn>
                    <inn>01.07.1907</inn>
                </forseti>
                <forseti id="2">
                    <þing>2</þing>
                    <nafn>Jón Jacobson</nafn>
                    <inn>01.07.1907</inn>
                </forseti>
                <forseti id="3">
                    <þing>2</þing>
                    <nafn>Jón Jacobson</nafn>
                    <inn>01.07.1907</inn>
                </forseti>
                <forseti id="4">
                    <þing>3</þing>
                    <nafn>Jón Jacobson</nafn>
                    <inn>01.07.1907</inn>
                </forseti>
            </forsetalisti>
            ';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
