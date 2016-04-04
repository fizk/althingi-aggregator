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

class AssemblyControllerTest extends AbstractConsoleControllerTestCase
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

    public function testTrue()
    {

        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/loggjafarthing',
            $this->getDomDocument()
        );

        $this->dispatch('load:assembly');

        $objects = $this->consumer->getObjects();

        $this->assertCount(1, $objects);
        $this->assertArrayHasKey('loggjafarthing/1', $objects);

        print_r($objects);

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
