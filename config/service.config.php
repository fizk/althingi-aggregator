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
            $rotatingLogHandler = new \Monolog\Handler\RotatingFileHandler('./data/log/althingi.log', 2, \Monolog\Logger::API);
            $rotatingLogHandler->setFormatter(new \Monolog\Formatter\LineFormatter());

            $rotatingErrorHandler = new \Monolog\Handler\RotatingFileHandler('./data/log/althingi.error.json', 2, \Monolog\Logger::WARNING);
            $rotatingErrorHandler->setFormatter(new \Monolog\Formatter\JsonFormatter(\Monolog\Formatter\JsonFormatter::BATCH_MODE_NEWLINES));

            $consoleHandler = new \Monolog\Handler\StreamHandler('php://stdout');
            $consoleHandler->setFormatter(new Bramus\Monolog\Formatter\ColoredLineFormatter());

            return (new \Monolog\Logger('althingi'))
                ->pushHandler($consoleHandler)
                ->pushHandler($rotatingLogHandler)
                ->pushHandler($rotatingErrorHandler);
        },

        'Cache' => function ($sm) {
            return getenv('AGGREGATE_NO_CACHE')
                ? new class extends Zend\Cache\Storage\Adapter\Filesystem {
                    public function getItem($key, & $success = null, & $casToken = null)
                    {
                        return null;
                    }
                    public function hasItem($key)
                    {
                        return false;
                    }
                    public function setItem($key, $value)
                    {
                        return false;
                    }
                }
                : new \Zend\Cache\Storage\Adapter\Filesystem();
        },

        'Provider' => function ($sm) {
            return (new \AlthingiAggregator\Lib\Provider\ServerProvider())
                ->setClient(new \Zend\Http\Client())
                ->setCache($sm->get('Cache'))
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
