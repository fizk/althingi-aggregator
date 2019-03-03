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
        return $cb(file_get_contents(__DIR__ . '/../data/raedur_bradabirgda.a.sml'));
    }

    public function getDomDocumentA()
    {
        $source = file_get_contents(__DIR__ . '/../data/speech.a.xml');
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }

    public function getIssueDocumentA()
    {
        $source = file_get_contents(__DIR__ . '/../data/issue.a.xml');
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }

    public function getSMLDocumentB()
    {
        $cb = new TemporarySpeechDocumentCallback('https://www.althingi.is/xml/148/raedur_bradabirgda');
        return $cb(file_get_contents(__DIR__ . '/../data/raedur_bradabirgda.b.sml'));
    }

    public function getDomDocumentB()
    {
        $source = file_get_contents(__DIR__ . '/../data/speech.b.xml');
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }

    public function getIssueDocumentB()
    {
        $source = file_get_contents(__DIR__ . '/../data/issue.b.xml');
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
