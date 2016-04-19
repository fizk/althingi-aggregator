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
            $fileHandler = new \Monolog\Handler\StreamHandler(
                './data/log/althingi.log',
                \Monolog\Logger::WARNING
            );

            $consoleHandler = new \Monolog\Handler\StreamHandler('php://stdout');
            $consoleHandler->setFormatter(new Bramus\Monolog\Formatter\ColoredLineFormatter());

            return (new \Monolog\Logger('althingi'))
                ->pushHandler($consoleHandler)
                ->pushHandler($fileHandler);
        },

        'Provider' => function ($sm) {
            return (new \AlthingiAggregator\Lib\Provider\ServerProvider(['save' => true]))
                ->setClient(new \Zend\Http\Client())
                ->setLogger($sm->get('Psr\Log'));
        },

        'Consumer' => function ($sm) {
            return (new \AlthingiAggregator\Lib\Consumer\RestServerConsumer())
                ->setClient(new \Zend\Http\Client())
                ->setConfig($sm->get('Config'))
                ->setLogger($sm->get('Psr\Log'));
//            return (new \AlthingiAggregator\Lib\Consumer\NullConsumer())
//                ->setConfig($sm->get('Config'))
//                ->setLogger($sm->get('Psr\Log'));
        }
    ],

    'initializers' => [],
];
