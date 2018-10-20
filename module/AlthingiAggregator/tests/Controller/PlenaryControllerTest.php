<?php
namespace AlthingiAggregatorTest\Controller;

use AlthingiAggregatorTest\Lib\Consumer\TestConsumer;
use AlthingiAggregatorTest\Lib\Provider\TestProvider;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class PlenaryControllerTest extends AbstractConsoleControllerTestCase
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

    public function testPlenaryRouter()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingfundir/?lthing=145',
            $this->getDomDocument()
        );

        $this->dispatch('load:plenary --assembly=145');

        $this->assertControllerClass('PlenaryController');
        $this->assertActionName('find-plenary');
    }

    public function testPlenaryData()
    {
        $this->provider->addDocument(
            'http://www.althingi.is/altext/xml/thingfundir/?lthing=145',
            $this->getDomDocument()
        );

        $this->dispatch('load:plenary --assembly=145');

        $consumerStoredData = $this->consumer->getObjects();

        $this->assertCount(2, $consumerStoredData);
        $this->assertArrayHasKey('loggjafarthing/145/thingfundir/0', $consumerStoredData);
        $this->assertArrayHasKey('loggjafarthing/145/thingfundir/1', $consumerStoredData);
    }

    public function getDomDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <þingfundir>
              <þingfundur númer="0">
                <fundarheiti>þingsetningarfundur</fundarheiti>
                <hefst>
                  <texti> 1. október, kl.  2:00 miðdegis</texti>
                  <dagur>01.10.2007</dagur>
                  <timi>14:00</timi>
                  <dagurtími>2007-10-01T14:00:00</dagurtími>
                </hefst>
                <fundursettur>2007-10-01T14:13:00</fundursettur>
                <fuslit>2007-10-01T14:49:43</fuslit>
                <sætaskipan>http://www.althingi.is/altext/xml/saetaskipan/?timi=2007-10-01T14:13:00</sætaskipan>
                <fundarskjöl>
                  <sgml>http://www.althingi.is/altext/135/f000.sgml</sgml>
                  <xml>http://www.althingi.is/xml/135/fundir/fun000.xml</xml>
                </fundarskjöl>
                <dagskrá>
                  <xml>http://www.althingi.is/altext/xml/dagskra/thingfundur/?lthing=135&amp;fundur=0</xml>
                </dagskrá>
              </þingfundur>
              <þingfundur númer="1">
                <fundarheiti>framhald þingsetningarfundar</fundarheiti>
                <hefst>
                  <texti> 1. október, kl.  4:00 síðdegis</texti>
                  <dagur>01.10.2007</dagur>
                  <timi>16:00</timi>
                  <dagurtími>2007-10-01T16:00:00</dagurtími>
                </hefst>
                <fundursettur>2007-10-01T15:59:57</fundursettur>
                <fuslit>2007-10-01T16:16:05</fuslit>
                <sætaskipan>http://www.althingi.is/altext/xml/saetaskipan/?timi=2007-10-01T15:59:57</sætaskipan>
                <fundarskjöl>
                  <sgml>http://www.althingi.is/altext/135/f001.sgml</sgml>
                  <xml>http://www.althingi.is/xml/135/fundir/fun001.xml</xml>
                </fundarskjöl>
                <dagskrá>
                  <xml>http://www.althingi.is/altext/xml/dagskra/thingfundur/?lthing=135&amp;fundur=1</xml>
                </dagskrá>
              </þingfundur>
            </þingfundir>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
