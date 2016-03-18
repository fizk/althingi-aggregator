<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 17/05/15
 * Time: 9:04 PM
 */

return [
    'invokables' => [
    ],

    'factories' => [
        'MessageStrategy' => 'Rend\View\Strategy\MessageFactory',

        'HttpClient' => function ($sm) {
            return new \Zend\Http\Client();
        },

        'Psr\Log' => function ($sm) {
            $logger = new \Monolog\Logger('althingi');
            $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout'));
            return $logger;
        },
    ],

    'initializers' => [

        'AlthingiAggregator\Lib\LoggerAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \AlthingiAggregator\Lib\LoggerAwareInterface) {
                $instance->setLogger($sm->get('Psr\Log'));
            }
        }
    ],
];
