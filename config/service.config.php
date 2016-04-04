<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/05/15
 * Time: 9:04 PM
 */

return [
    'invokables' => [],

    'factories' => [
        'Psr\Log' => function ($sm) {
            $handler = new \Monolog\Handler\StreamHandler('php://stdout');
            $handler->setFormatter(new Bramus\Monolog\Formatter\ColoredLineFormatter());
            $logger = new \Monolog\Logger('althingi');
            $logger->pushHandler($handler);
            return $logger;
        },

        'Provider' => function ($sm) {
            $client = (new \Zend\Http\Client());
//                ->setAdapter(new AlthingiAggregator\Lib\Http\Client\Adapter\LocalXmlAdapter())
//                ->setOptions(['protocol' => './']);

            return (new \AlthingiAggregator\Lib\Provider\ServerProvider(['save' => true]))
                ->setClient($client);
        },

        'Consumer' => function ($sm) {
            return (new \AlthingiAggregator\Lib\Consumer\RestServerConsumer())
                ->setClient(new \Zend\Http\Client())
                ->setConfig($sm->get('Config'))
                ->setLogger($sm->get('Psr\Log'));
        }
    ],

    'initializers' => [],
];
