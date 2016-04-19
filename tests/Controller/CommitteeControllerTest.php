<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 30/03/2016
 * Time: 8:08 PM
 */

namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Lib\Consumer\TestConsumer;
use AlthingiAggregator\Lib\Provider\TestProvider;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class CommitteeControllerTest extends AbstractConsoleControllerTestCase
{
    /** @var  TestConsumer */
    private $consumer;

    /** @var  TestProvider */
    private $provider;

    public function setUp()
    {
        $this->setApplicationConfig(include __DIR__ .'/../application.config.php');
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
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/nefndir',
            $this->getDomDocument()
        );

        $this->dispatch('load:committee');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('find-committee');
    }

    public function testAssemblyByNumberData()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/nefndir',
            $this->getDomDocument()
        );

        $this->dispatch('load:committee');

        $consumerStoredData = $this->consumer->getObjects();

        $this->assertCount(2, $consumerStoredData);
        $this->assertArrayHasKey('nefndir/201', $consumerStoredData);
        $this->assertArrayHasKey('nefndir/151', $consumerStoredData);

    }

    public function getDomDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <nefndir>
              <nefnd id="201">
                <heiti>allsherjar- og menntamálanefnd</heiti>
                <skammstafanir>
                  <stuttskammstöfun>am</stuttskammstöfun>
                  <löngskammstöfun>allsh.- og menntmn.</löngskammstöfun>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>140</fyrstaþing>
                </tímabil>
              </nefnd>
              <nefnd id="151">
                <heiti>allsherjarnefnd</heiti>
                <skammstafanir>
                  <stuttskammstöfun>a</stuttskammstöfun>
                  <löngskammstöfun>allshn.</löngskammstöfun>
                </skammstafanir>
                <tímabil>
                  <fyrstaþing>27</fyrstaþing>
                  <síðastaþing>139</síðastaþing>
                </tímabil>
              </nefnd>
            </nefndir>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
