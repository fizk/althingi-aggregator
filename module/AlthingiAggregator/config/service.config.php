<?php

return [
    'invokables' => [],

    'factories' => [
        'Psr\Log' => function () {
            $handlers = [];
            $logger = (new \Monolog\Logger('althingi-aggregator'))
                ->pushProcessor(new \Monolog\Processor\MemoryPeakUsageProcessor(true, false))
                ->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor(true, false));

            if (! empty(getenv('LOG_PATH')) && strtolower(getenv('LOG_PATH')) !== 'none' && getenv('LOG_PATH')) {
                $handlers[] = new \Monolog\Handler\StreamHandler(getenv('LOG_PATH') ?: 'php://stdout');
            }

            $formattedHandlers = array_map(function (\Monolog\Handler\HandlerInterface $handler) {
                switch (strtolower(getenv('LOG_FORMAT'))) {
                    case 'logstash':
                        $handler->setFormatter(new \Monolog\Formatter\LogstashFormatter('althingi-aggregator'));
                        break;
                    case 'json':
                        $handler->setFormatter(new \Monolog\Formatter\JsonFormatter());
                        break;
                    case 'line':
                        $handler->setFormatter(new \Monolog\Formatter\LineFormatter());
                        break;
                    case 'color':
                        $handler->setFormatter(new \Bramus\Monolog\Formatter\ColoredLineFormatter());
                        break;
                }
                return $handler;
            }, $handlers);

            array_walk($formattedHandlers, function ($handler) use ($logger) {
                $logger->pushHandler($handler);
            });

            return $logger;
        },

        'ConsumerCache' => function () {
            // In-memory cache (Redis)
            if (strtolower(getenv('CONSUMER_CACHE_TYPE')) === 'memory') {
                $memoryConfig = (new Zend\Cache\Storage\Adapter\RedisOptions())->setServer([
                    'host' => getenv('CONSUMER_CACHE_HOST') ?: 'localhost',
                    'port' => getenv('CONSUMER_CACHE_PORT') ?: '6379'
                ])->setTtl(60 * 60 * 24 * 2);

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
            } elseif (strtolower(getenv('CONSUMER_CACHE_TYPE')) === 'file') {
                $fileConfig = (new Zend\Cache\Storage\Adapter\FilesystemOptions())
                    ->setCacheDir('./data/cache/consumer')
                    ->setTtl(60 * 60 * 24 * 2)
                    ->setNamespace('consumer');
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
            // In-memory cache (Redis)
            if (strtolower(getenv('PROVIDER_CACHE_TYPE')) === 'memory') {
                $memoryConfig = (new Zend\Cache\Storage\Adapter\RedisOptions())->setServer([
                    'host' => getenv('PROVIDER_CACHE_HOST') ?: 'localhost',
                    'port' => getenv('PROVIDER_CACHE_PORT') ?: '6379'
                ])->setTtl(60 * 60 * 12);
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
            } elseif (strtolower(getenv('PROVIDER_CACHE_TYPE')) === 'file') {
                $fileConfig = (new Zend\Cache\Storage\Adapter\FilesystemOptions())
                    ->setCacheDir('./data/cache/provider')
                    ->setTtl(60 * 60 * 12)
                    ->setNamespace('provider');
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

        'MediaClient' => function () {
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
