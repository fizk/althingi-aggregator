<?php

return [
    'invokables' => [],

    'factories' => [
        'Psr\Log' => function ($sm) {
            $logger = (new \Monolog\Logger('althingi'))
                ->pushProcessor(new \Monolog\Processor\MemoryPeakUsageProcessor())
                ->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor());

            if (getenv('LOGGER_SAVE') === 'true') {
                $rotatingLogHandler = new \Monolog\Handler\RotatingFileHandler('./data/log/althingi.log', 2, \Monolog\Logger::API);
                $rotatingLogHandler->setFormatter(new \Monolog\Formatter\LineFormatter());

                $rotatingErrorHandler = new \Monolog\Handler\RotatingFileHandler('./data/log/althingi.error.json', 2, \Monolog\Logger::WARNING);
                $rotatingErrorHandler->setFormatter(new \Monolog\Formatter\LineFormatter());

                $logger->pushHandler($rotatingLogHandler);
                $logger->pushHandler($rotatingErrorHandler);
            }

            if (getenv('LOGGER_STREAM') === 'true') {
                $consoleHandler = new \Monolog\Handler\StreamHandler('php://stdout');

                switch (strtolower(getenv('LOGGER_FORMAT'))) {
                    case 'logstash':
                        $consoleHandler->setFormatter(new \Monolog\Formatter\LogstashFormatter('althingi-aggregator'));
                        $logger->pushHandler($consoleHandler);
                        break;
                    case 'json':
                        $consoleHandler->setFormatter(new \Monolog\Formatter\JsonFormatter());
                        $logger->pushHandler($consoleHandler);
                        break;
                    case 'line':
                        $consoleHandler->setFormatter(new \Monolog\Formatter\LineFormatter());
                        $logger->pushHandler($consoleHandler);
                        break;
                    case 'color':
                        $consoleHandler->setFormatter(new Bramus\Monolog\Formatter\ColoredLineFormatter());
                        $logger->pushHandler($consoleHandler);
                        break;
                }
            }

            return $logger;
        },

        'ConsumerCache' => function () {
            $memoryConfig = (new Zend\Cache\Storage\Adapter\RedisOptions())->setServer([
                'host' => getenv('CONSUMER_CACHE_HOST') ?: 'localhost',
                'port' => getenv('CONSUMER_CACHE_PORT') ?: '6379'
            ])->setTtl(60*60*24*2);
            $fileConfig = (new Zend\Cache\Storage\Adapter\FilesystemOptions())
                ->setCacheDir('./data/cache/consumer')
                ->setTtl(60*60*24*2)
                ->setNamespace('consumer');

            // In-memory cache (Redis)
            if (strtolower(getenv('CONSUMER_CACHE_TYPE')) === 'memory') {
                if (strtolower(getenv('CONSUMER_CACHE')) === 'true') {
                    return new \Zend\Cache\Storage\Adapter\Redis($memoryConfig);
                }
                return new class($memoryConfig) extends \Zend\Cache\Storage\Adapter\Redis {
                    public function getItem($key, & $success = null, & $casToken = null)
                    {
                        return null;
                    }
                    public function hasItem($key)
                    {
                        return false;
                    }
                };
            // FileSystem cache (for development)
            } else if (strtolower(getenv('CONSUMER_CACHE_TYPE')) === 'file') {
                // Get data from cache (file-system)
                if (strtolower(getenv('CONSUMER_CACHE')) === 'true') {
                    return new \Zend\Cache\Storage\Adapter\Filesystem($fileConfig);
                }
                // Don't get data from cache
                return new class ($fileConfig) extends \Zend\Cache\Storage\Adapter\Filesystem {
                    public function getItem($key, & $success = null, & $casToken = null)
                    {
                        return null;
                    }
                    public function hasItem($key)
                    {
                        return false;
                    }
                };
            }

            // No cache
            return new \Zend\Cache\Storage\Adapter\BlackHole();
        },

        'ProviderCache' => function () {
            $memoryConfig = (new Zend\Cache\Storage\Adapter\RedisOptions())->setServer([
                'host' => getenv('PROVIDER_CACHE_HOST') ?: 'localhost',
                'port' => getenv('PROVIDER_CACHE_PORT') ?: '6379'
            ])->setTtl(60*60*24*2);
            $fileConfig = (new Zend\Cache\Storage\Adapter\FilesystemOptions())
                ->setCacheDir('./data/cache/provider')
                ->setTtl(60*60*24*2)
                ->setNamespace('provider');

            // In-memory cache (Redis)
            if (strtolower(getenv('PROVIDER_CACHE_TYPE')) === 'memory') {
                // Get data from cache (in-memory)
                if (strtolower(getenv('PROVIDER_CACHE')) === 'true') {
                    return new \Zend\Cache\Storage\Adapter\Redis($memoryConfig);
                }
                // Don't get data from cache
                return new class($memoryConfig) extends \Zend\Cache\Storage\Adapter\Redis {
                    public function getItem($key, & $success = null, & $casToken = null)
                    {
                        return null;
                    }
                    public function hasItem($key)
                    {
                        return false;
                    }
                };
            // FileSystem cache (for development)
            } else if (strtolower(getenv('PROVIDER_CACHE_TYPE')) === 'file') {
                // Get data from cache (file-system)
                if (strtolower(getenv('PROVIDER_CACHE')) === 'true') {
                    return new \Zend\Cache\Storage\Adapter\Filesystem($fileConfig);
                }
                // Don't get data from cache
                return new class($fileConfig) extends \Zend\Cache\Storage\Adapter\Filesystem {
                    public function getItem($key, & $success = null, & $casToken = null)
                    {
                        return null;
                    }
                    public function hasItem($key)
                    {
                        return false;
                    }
                };
            }
            // No cache
            return new \Zend\Cache\Storage\Adapter\BlackHole();
        },

        'Provider' => function ($sm) {
            return (new \AlthingiAggregator\Lib\Provider\ServerProvider())
                ->setClient(new \Zend\Http\Client())
                ->setCache($sm->get('ProviderCache'))
                ->setLogger($sm->get('Psr\Log'));
        },

        'MediaClient' => function ($sm) {
            return (new \AlthingiAggregator\Lib\MediaClient\ThumborClient())
                ->setClient(new \Zend\Http\Client())
                ->setUri('http://127.0.0.1:8000/image');

//            return (new \AlthingiAggregator\Lib\MediaClient\FileSystemClient())
//                ->setClient(new \Zend\Http\Client())
//                ->setBase('./data/avatar');
        },

        'Consumer' => function ($sm) {

//            return (new \AlthingiAggregator\Lib\Consumer\NullConsumer())
//                ->setLogger($sm->get('Psr\Log'));

            return (new \AlthingiAggregator\Lib\Consumer\HttpConsumer())
                ->setCache($sm->get('ConsumerCache'))
                ->setClient(new \Zend\Http\Client())
                ->setLogger($sm->get('Psr\Log'))
                ->setMediaClient($sm->get('MediaClient'))
                ->setUri(new Zend\Uri\Http(
                    getenv('AGGREGATOR_CONSUMER') ? : 'http://localhost:8080'
                ));
        }
    ],

    'initializers' => [],
];
