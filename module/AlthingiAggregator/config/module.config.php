<?php

namespace AlthingiAggregator;

use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Router\Http\Regex;

return array(
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
    'controllers' =>[
        'factories' => [
            Controller\IndexController::class =>  InvokableFactory::class,
            Controller\HelpController::class => InvokableFactory::class,
            Controller\AssemblyController::class => function ($container) {
                return (new Controller\AssemblyController())
                    ->setConsumer($container->get('Consumer'))
                    ->setProvider($container->get('Provider'));
            },
            Controller\CongressmanController::class => function ($container) {
                return (new Controller\CongressmanController())
                    ->setConsumer($container->get('Consumer'))
                    ->setProvider($container->get('Provider'));
            },
            Controller\ConstituencyController::class => function ($container) {
                return (new Controller\ConstituencyController())
                    ->setConsumer($container->get('Consumer'))
                    ->setProvider($container->get('Provider'));
            },
            Controller\IssueController::class => function ($container) {
                return (new Controller\IssueController())
                    ->setConsumer($container->get('Consumer'))
                    ->setProvider($container->get('Provider'));
            },
            Controller\PartyController::class => function ($container) {
                return (new Controller\PartyController())
                    ->setConsumer($container->get('Consumer'))
                    ->setProvider($container->get('Provider'));
            },
            Controller\PlenaryController::class => function ($container) {
                return (new Controller\PlenaryController())
                    ->setConsumer($container->get('Consumer'))
                    ->setProvider($container->get('Provider'));
            },
            Controller\CommitteeController::class => function ($container) {
                return (new Controller\CommitteeController())
                    ->setConsumer($container->get('Consumer'))
                    ->setProvider($container->get('Provider'));
            },
            Controller\PresidentController::class => function ($container) {
                return (new Controller\PresidentController())
                    ->setConsumer($container->get('Consumer'))
                    ->setProvider($container->get('Provider'));
            },
            Controller\CategoryController::class => function ($container) {
                return (new Controller\CategoryController())
                    ->setConsumer($container->get('Consumer'))
                    ->setProvider($container->get('Provider'));
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
                'plenary' => [
                    'options' => [
                        'route'    => 'load:plenary [--assembly=|-a]',
                        'defaults' => [
                            'controller' => Controller\PlenaryController::class,
                            'action'     => 'find-plenary'
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
                        'route'    => 'load:president',
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
            ],
        ],
    ],
);
