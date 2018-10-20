<?php
namespace AlthingiAggregatorTest\Controller;

use AlthingiAggregatorTest\Lib\Consumer\TestConsumer;
use AlthingiAggregatorTest\Lib\Provider\TestProvider;
use AlthingiAggregator\Lib\Provider\ProviderInterface;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;
use Zend\Stdlib\ArrayUtils;

class AssemblyControllerTest extends AbstractConsoleControllerTestCase
{
    /** @var  TestConsumer */
    private $consumer;

    /** @var  TestProvider */
    private $provider;

    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
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

    public function testAssemblyByNumberRouter()
    {
        $this->provider = new class implements ProviderInterface {
            public function get($url)
            {
                return new \DOMDocument();
//                throw new \Zend\Http\Exception\RuntimeException();
//            * @throws Client\Exception\RuntimeException
            }

            public function addDocument($url, \DOMDocument $dom)
            {
            }
        };


//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Provider', $this->provider);


        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/loggjafarthing',
            $this->getDomDocument()
        );

        $this->dispatch('load:assembly');

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('find-assembly');
    }

    public function testAssemblyByNumberData()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/loggjafarthing',
            $this->getDomDocument()
        );

        $this->dispatch('load:assembly');

        $consumerStoredData = $this->consumer->getObjects();

        $this->assertCount(1, $consumerStoredData);
        $this->assertArrayHasKey('loggjafarthing/1', $consumerStoredData);
    }

    public function testAssemblyCurrentRouter()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/loggjafarthing/yfirstandandi',
            $this->getDomDocument()
        );

        $this->dispatch('load:assembly:current');

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('current-assembly');
    }

    public function testAssemblyCurrentData()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/loggjafarthing/yfirstandandi',
            $this->getDomDocument()
        );

        $this->dispatch('load:assembly:current');

        $consumerStoredData = $this->consumer->getObjects();

        $this->assertCount(1, $consumerStoredData);
        $this->assertArrayHasKey('loggjafarthing/1', $consumerStoredData);
    }

    public function getDomDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <löggjafarþing>
                <þing númer="1">
                    <tímabil>1875</tímabil>
                    <þingsetning>01.07.1875</þingsetning>
                    <þinglok>26.08.1875</þinglok>
                </þing>
            </löggjafarþing>
            ';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
