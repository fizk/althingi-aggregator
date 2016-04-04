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

class CongressmanControllerTest extends AbstractConsoleControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testTrue()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Provider', new TestProvider());
        $serviceManager->setService('Consumer', new TestConsumer());

        $this->dispatch('load:congressman --assembly=145');

        $this->assertConsoleOutputContains("This is my incredible CLI command!");
    }
}
