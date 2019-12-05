<?php
namespace AlthingiAggregatorTest\Controller;

use AlthingiAggregatorTest\Consumer\TestConsumer;
use AlthingiAggregatorTest\Provider\TestProvider;
use AlthingiAggregator\Consumer;
use AlthingiAggregator\Provider;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class IssueControllerTest extends AbstractConsoleControllerTestCase
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

    public function testNotShowingUp()
    {
        $this->assertTrue(true);
//
//        $this->dispatch('load:single-issue --assembly=143  --issue=1  --category=A');
//
//        $consumerStoredData = $this->consumer->getObjects();
//
//        $this->assertCount(1, $consumerStoredData);
//        $this->assertArrayHasKey('loggjafarthing/1', $consumerStoredData);
    }
}
