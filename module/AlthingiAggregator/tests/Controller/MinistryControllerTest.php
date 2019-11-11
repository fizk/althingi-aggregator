<?php
namespace AlthingiAggregatorTest\Controller;

use AlthingiAggregator\Controller\MinistryController;
use AlthingiAggregatorTest\Lib\Consumer\TestConsumer;
use AlthingiAggregatorTest\Lib\Provider\TestProvider;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;
use Zend\Stdlib\ArrayUtils;

class MinistryControllerTest extends AbstractConsoleControllerTestCase
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

    public function testMinistryRoute()
    {
        $this->provider->addDocument(
            'https://www.althingi.is/altext/xml/radherraembaetti',
            $this->getDomDocument()
        );

        $this->dispatch('load:ministry');

        $consumerStoredData = $this->consumer->getObjects();

        $this->assertCount(3, $consumerStoredData);
        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('find-ministry');
    }

    public function getDomDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <ráðherrar>
                <ráðherraembætti id="123">
                    <heiti> atvinnumálaráðherra </heiti>
                    <skammstafanir>
                        <stuttskammstöfun>atvmrh.</stuttskammstöfun>
                        <löngskammstöfun>atvinnumálarh.</löngskammstöfun>
                    </skammstafanir>
                    <tímabil>
                        <fyrstaþing>1</fyrstaþing>
                        <síðastaþing>100</síðastaþing>
                    </tímabil>
                </ráðherraembætti>
                <ráðherraembætti id="208">
                    <heiti> atvinnuvega- og nýsköpunarráðherra </heiti>
                    <skammstafanir>
                        <stuttskammstöfun>atvvrh.</stuttskammstöfun>
                        <löngskammstöfun>atv.- og nýskrh.</löngskammstöfun>
                    </skammstafanir>
                    <tímabil>
                        <fyrstaþing>140</fyrstaþing>
                        <síðastaþing>141</síðastaþing>
                    </tímabil>
                </ráðherraembætti>
                <ráðherraembætti id="186">
                    <heiti> dómsmála- og mannréttindaráðherra </heiti>
                    <skammstafanir>
                        <stuttskammstöfun>dómsmrh.</stuttskammstöfun>
                        <löngskammstöfun>dómsmálarh.</löngskammstöfun>
                    </skammstafanir>
                    <tímabil>
                        <fyrstaþing>138</fyrstaþing>
                        <síðastaþing>139</síðastaþing>
                    </tímabil>
                </ráðherraembætti>
            </ráðherrar>
            ';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
