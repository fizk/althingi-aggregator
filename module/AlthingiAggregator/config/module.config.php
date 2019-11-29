<?php

namespace AlthingiAggregator;

use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\Router\Http\Regex;
use AlthingiAggregator\Consumer;
use AlthingiAggregator\Provider;

return [
    'router' => [
        'routes' => [
            'index' => [
                'type' => Regex::class,
                'options' => [
                    'regex' => '(<path>((?:\w+:)?\/\/[^/]+([^?#]+)))',
                    'spec' => '%path%',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\HelpController::class => InvokableFactory::class,
            Controller\AssemblyController::class => function (ServiceManager $container) {
                return (new Controller\AssemblyController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\CongressmanController::class => function (ServiceManager $container) {
                return (new Controller\CongressmanController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\ConstituencyController::class => function (ServiceManager $container) {
                return (new Controller\ConstituencyController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\IssueController::class => function (ServiceManager $container) {
                return (new Controller\IssueController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\PartyController::class => function (ServiceManager $container) {
                return (new Controller\PartyController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\PlenaryController::class => function (ServiceManager $container) {
                return (new Controller\PlenaryController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\CommitteeController::class => function (ServiceManager $container) {
                return (new Controller\CommitteeController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\PresidentController::class => function (ServiceManager $container) {
                return (new Controller\PresidentController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\CategoryController::class => function (ServiceManager $container) {
                return (new Controller\CategoryController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\InflationController::class => function (ServiceManager $container) {
                return (new Controller\InflationController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\GovernmentController::class => function (ServiceManager $container) {
                return (new Controller\GovernmentController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\SpeechController::class => function (ServiceManager $container) {
                return (new Controller\SpeechController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
            Controller\MinistryController::class => function (ServiceManager $container) {
                return (new Controller\MinistryController())
                    ->setConsumer($container->get(Consumer\ConsumerInterface::class))
                    ->setProvider($container->get(Provider\ProviderInterface::class));
            },
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'help' => [
                    'options' => [
                        'route'    => '',
                        'defaults' => [
                            'controller' => Controller\HelpController::class,
                            'action'     => 'index'
                        ]
                    ]
                ],
                'load-assembly' => [
                    'options' => [
                        'route'    => 'load:assembly',
                        'defaults' => [
                            'controller' => Controller\AssemblyController::class,
                            'action'     => 'find-assembly'
                        ]
                    ]
                ],
                'party' => [
                    'options' => [
                        'route'    => 'load:party',
                        'defaults' => [
                            'controller' => Controller\PartyController::class,
                            'action'     => 'find-party'
                        ]
                    ]
                ],
                'constituency' => [
                    'options' => [
                        'route'    => 'load:constituency',
                        'defaults' => [
                            'controller' => Controller\ConstituencyController::class,
                            'action'     => 'find-constituency'
                        ]
                    ]
                ],
                'current-assembly' => [
                    'options' => [
                        'route'    => 'load:assembly:current',
                        'defaults' => [
                            'controller' => Controller\AssemblyController::class,
                            'action'     => 'current-assembly'
                        ]
                    ]
                ],
                'congressman' => [
                    'options' => [
                        'route'    => 'load:congressman [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Controller\CongressmanController::class,
                            'action'     => 'find-congressman'
                        ]
                    ]
                ],
                'minister' => [
                    'options' => [
                        'route'    => 'load:minister [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Controller\CongressmanController::class,
                            'action'     => 'find-minister'
                        ]
                    ]
                ],
                'ministry' => [
                    'options' => [
                        'route'    => 'load:ministry',
                        'defaults' => [
                            'controller' => Controller\MinistryController::class,
                            'action'     => 'find-ministry'
                        ]
                    ]
                ],
                'plenary' => [
                    'options' => [
                        'route'    => 'load:plenary [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Controller\PlenaryController::class,
                            'action'     => 'find-plenary'
                        ]
                    ]
                ],
                'plenary-agenda' => [
                    'options' => [
                        'route'    => 'load:plenary-agenda [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Controller\PlenaryController::class,
                            'action'     => 'find-plenary-agenda'
                        ]
                    ]
                ],
                'issue' => [
                    'options' => [
                        'route'    => 'load:issue [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Controller\IssueController::class,
                            'action'     => 'find-issue'
                        ]
                    ]
                ],
                'single-issue' => [
                    'options' => [
                        'route'    => 'load:single-issue [--assembly=|-a]  [--issue=|-i]  [--category=|-c]',
                        'defaults' => [
                            'controller' => Controller\IssueController::class,
                            'action'     => 'find-single-issue'
                        ]
                    ]
                ],
                'committee' => [
                    'options' => [
                        'route'    => 'load:committee',
                        'defaults' => [
                            'controller' => Controller\CommitteeController::class,
                            'action'     => 'find-committee'
                        ]
                    ]
                ],
                'committee-assembly' => [
                    'options' => [
                        'route'    => 'load:committee-assembly [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Controller\CommitteeController::class,
                            'action'     => 'find-assembly-committee'
                        ]
                    ]
                ],
                'president' => [
                    'options' => [
                        'route'    => 'load:president [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Controller\PresidentController::class,
                            'action'     => 'find-president'
                        ]
                    ]
                ],
                'categories' => [
                    'options' => [
                        'route'    => 'load:category',
                        'defaults' => [
                            'controller' => Controller\CategoryController::class,
                            'action'     => 'find-categories'
                        ]
                    ]
                ],
                'inflation' => [
                    'options' => [
                        'route'    => 'load:inflation [--date=|-d]',
                        'defaults' => [
                            'controller' => Controller\InflationController::class,
                            'action'     => 'find-inflation'
                        ]
                    ]
                ],
                'government' => [
                    'options' => [
                        'route'    => 'load:government',
                        'defaults' => [
                            'controller' => Controller\GovernmentController::class,
                            'action'     => 'find-governments'
                        ]
                    ]
                ],
                'tmp-speech' => [
                    'options' => [
                        'route'    => 'load:tmp-speech [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Controller\SpeechController::class,
                            'action'     => 'find-temporary'
                        ]
                    ]
                ],
            ],
        ],
    ],
];
