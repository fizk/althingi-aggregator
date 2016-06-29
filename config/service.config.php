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

        'Cache' => function ($sm) {
            $cache = new \Zend\Cache\Storage\Adapter\Filesystem();
            $cache->getOptions()->setTtl(60*60*60*365);
            $cache->getOptions()->setCacheDir('./data/cache');
            
            return $cache;
        },

        'Provider' => function ($sm) {
            return (new \AlthingiAggregator\Lib\Provider\ServerProvider(['save' => true]))
                ->setClient(new \Zend\Http\Client())
                ->setLogger($sm->get('Psr\Log'));
        },

        'Consumer' => function ($sm) {
            $config = $sm->get('Config');
            
            return (new \AlthingiAggregator\Lib\Consumer\HttpConsumer())
                ->setCache($sm->get('Cache'))
                ->setClient(new \Zend\Http\Client())
                ->setLogger($sm->get('Psr\Log'))
                ->setUri(new Zend\Uri\Http($config['server']['host']));
        }
    ],

    'initializers' => [],
];
