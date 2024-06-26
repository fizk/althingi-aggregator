<?php

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Container\ContainerInterface;
use League\Event\EventDispatcher;
use League\Event\PrioritizedListenerRegistry;
use Laminas\Diactoros\Uri;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Buzz\Client\Curl;
use Nyholm\Psr7\Factory\Psr17Factory;
use App\Provider;
use App\Consumer;
use App\Event\{
    ConsumerResponseEvent,
    ErrorEvent,
    ProviderResponseEvent,
    SystemSuccessEvent
};

return [
    'factories' => [
        App\Handler\Help::class => function () {
            return new App\Handler\Help();
        },
        App\Handler\Assembly\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Assembly\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Assembly\Current::class => function (ContainerInterface $container) {
            return (new App\Handler\Assembly\Current())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Party\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Party\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Constituency\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Constituency\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Congressman\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Congressman\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Congressman\Minister::class => function (ContainerInterface $container) {
            return (new App\Handler\Congressman\Minister())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Ministry\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Ministry\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Plenary\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Plenary\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Plenary\Agenda::class => function (ContainerInterface $container) {
            return (new App\Handler\Plenary\Agenda())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Committee\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Committee\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Committee\Assembly::class => function (ContainerInterface $container) {
            return (new App\Handler\Committee\Assembly())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\President\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\President\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Category\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Category\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Inflation\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Inflation\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Government\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Government\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Speech\Temporary::class => function (ContainerInterface $container) {
            return (new App\Handler\Speech\Temporary())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Issue\Single::class => function (ContainerInterface $container) {
            return (new App\Handler\Issue\Single())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },
        App\Handler\Issue\Find::class => function (ContainerInterface $container) {
            return (new App\Handler\Issue\Find())
                ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                ->setProvider($container->get(Provider\ProviderInterface::class))
                ;
        },

        Psr\Http\Client\ClientInterface::class => function (ContainerInterface $container, $requestedName) {
            return new Curl(new Psr17Factory(), ['allow_redirects' => true]);
        },
        Psr\Log\LoggerInterface::class => function (ContainerInterface $container, $requestedName) {
            return (new Logger('aggregator'))
                ->pushHandler((new StreamHandler('php://stdout', Logger::DEBUG))
                    ->setFormatter(new LineFormatter("[%datetime%] %level_name% %message%\n")));
        },

        EventDispatcherInterface::class => function (ContainerInterface $container, $requestedName) {
            $logger = $container->get(Psr\Log\LoggerInterface::class);
            $provider = new PrioritizedListenerRegistry();

            $provider->subscribeTo(ProviderResponseEvent::class, function (ProviderResponseEvent $event) use ($logger) {
                $logger->debug((string) $event);
            });
            $provider->subscribeTo(ConsumerResponseEvent::class, function (ConsumerResponseEvent $event) use ($logger) {
                $logger->debug((string) $event);
            });
            $provider->subscribeTo(ErrorEvent::class, function (ErrorEvent $event) use ($logger) {
                $logger->error((string) $event);
            });
            $provider->subscribeTo(SystemSuccessEvent::class, function (SystemSuccessEvent $event) use ($logger) {
                $logger->debug((string) $event);
            });

            return new EventDispatcher($provider);
        },

        Provider\ProviderInterface::class => function (ContainerInterface $container) {
            return (new Provider\ServerProvider())
                ->setHttpClient($container->get(Psr\Http\Client\ClientInterface::class))
                ->setCache($container->get('ProviderCache'))
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },

        Consumer\ConsumerInterface::class => function (ContainerInterface $container) {
            $uri = (new Uri())
                ->withHost(getenv('AGGREGATOR_CONSUMER_HOST') ?: 'localhost')
                ->withPort(getenv('AGGREGATOR_CONSUMER_PORT') ?: '8080')
                ->withScheme(getenv('AGGREGATOR_CONSUMER_SCHEMA') ?: 'http')
                ;
            return (new Consumer\HttpConsumer(
                getenv('ENVIRONMENT') === 'DEVELOPMENT' ?  ['x-set-response-status-code' => '201'] : []
            ))
                ->setHttpClient($container->get(Psr\Http\Client\ClientInterface::class))
                ->setCache($container->get('ConsumerCache'))
                ->setUri($uri)
                ->setEventDispatcher($container->get(EventDispatcherInterface::class))
                ;
        },

        'ConsumerCache' => function () {
            $cacheType = getenv('CONSUMER_CACHE_TYPE') ?: 'none';
            switch (strtolower($cacheType)) {
                case 'none':
                    return new \Laminas\Cache\Storage\Adapter\BlackHole();
                    break;
                default:
                    $memoryConfig = (new \Laminas\Cache\Storage\Adapter\RedisOptions())->setServer([
                        'host' => getenv('CONSUMER_CACHE_HOST') ?: 'localhost',
                        'port' => getenv('CONSUMER_CACHE_PORT') ?: '6379'
                    ])->setTtl(60 * 60 * 24 * 2);
                    return new \Laminas\Cache\Storage\Adapter\Redis($memoryConfig);
            }
        },

        'ProviderCache' => function () {
            $cacheType = getenv('PROVIDER_CACHE_TYPE') ?: 'none';
            switch (strtolower($cacheType)) {
                case 'none':
                    return new \Laminas\Cache\Storage\Adapter\BlackHole();
                    break;
                default:
                    $memoryConfig = (new \Laminas\Cache\Storage\Adapter\RedisOptions())->setServer([
                        'host' => getenv('PROVIDER_CACHE_HOST') ?: 'localhost',
                        'port' => getenv('PROVIDER_CACHE_PORT') ?: '6379'
                    ])->setTtl(60 * 60 * 12);
                    return new \Laminas\Cache\Storage\Adapter\Redis($memoryConfig);
                    break;
            }
        },
    ],
];
