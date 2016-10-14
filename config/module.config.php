<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => [
        'routes' => [
            'index' => [
                'type' => 'Zend\Mvc\Router\Http\Regex',
                'options' => [
                    'regex' => '(<path>((?:\w+:)?\/\/[^/]+([^?#]+)))',
                    'spec' => '%path%',
                    'defaults' => [
                        'controller' => 'AlthingiAggregator\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
        ]
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],

    'controllers' =>[
        'invokables' => [
            'AlthingiAggregator\Controller\Index' => 'AlthingiAggregator\Controller\IndexController',
            'AlthingiAggregator\Controller\Assembly' => 'AlthingiAggregator\Controller\AssemblyController',
            'AlthingiAggregator\Controller\Congressman' => 'AlthingiAggregator\Controller\CongressmanController',
            'AlthingiAggregator\Controller\Constituency' => 'AlthingiAggregator\Controller\ConstituencyController',
            'AlthingiAggregator\Controller\Issue' => 'AlthingiAggregator\Controller\IssueController',
            'AlthingiAggregator\Controller\Party' => 'AlthingiAggregator\Controller\PartyController',
            'AlthingiAggregator\Controller\Plenary' => 'AlthingiAggregator\Controller\PlenaryController',
            'AlthingiAggregator\Controller\Help' => 'AlthingiAggregator\Controller\HelpController',
            'AlthingiAggregator\Controller\Committee' => 'AlthingiAggregator\Controller\CommitteeController',
            'AlthingiAggregator\Controller\President' => 'AlthingiAggregator\Controller\PresidentController',
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [],
    ],
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [
                'help' => [
                    'options' => [
                        'route'    => '',
                        'defaults' => [
                            'controller' => 'AlthingiAggregator\Controller\Help',
                            'action'     => 'index'
                        ]
                    ]
                ],
                'load-assembly' => [
                    'options' => [
                        'route'    => 'load:assembly',
                        'defaults' => [
                            'controller' => 'AlthingiAggregator\Controller\Assembly',
                            'action'     => 'find-assembly'
                        ]
                    ]
                ],
                'party' => [
                    'options' => [
                        'route'    => 'load:party',
                        'defaults' => [
                            'controller' => 'AlthingiAggregator\Controller\Party',
                            'action'     => 'find-party'
                        ]
                    ]
                ],
                'constituency' => [
                    'options' => [
                        'route'    => 'load:constituency',
                        'defaults' => [
                            'controller' => 'AlthingiAggregator\Controller\Constituency',
                            'action'     => 'find-constituency'
                        ]
                    ]
                ],
                'current-assembly' => [
                    'options' => [
                        'route'    => 'load:assembly:current',
                        'defaults' => [
                            'controller' => 'AlthingiAggregator\Controller\Assembly',
                            'action'     => 'current-assembly'
                        ]
                    ]
                ],
                'congressman' => [
                    'options' => [
                        'route'    => 'load:congressman [--assembly=|-a]',
                        'defaults' => [
                            'controller' => 'AlthingiAggregator\Controller\Congressman',
                            'action'     => 'find-congressman'
                        ]
                    ]
                ],
                'plenary' => [
                    'options' => [
                        'route'    => 'load:plenary [--assembly=|-a]',
                        'defaults' => [
                            'controller' => 'AlthingiAggregator\Controller\Plenary',
                            'action'     => 'find-plenary'
                        ]
                    ]
                ],
                'issue' => [
                    'options' => [
                        'route'    => 'load:issue [--assembly=|-a]',
                        'defaults' => [
                            'controller' => 'AlthingiAggregator\Controller\Issue',
                            'action'     => 'find-issue'
                        ]
                    ]
                ],
                'committee' => [
                    'options' => [
                        'route'    => 'load:committee',
                        'defaults' => [
                            'controller' => 'AlthingiAggregator\Controller\Committee',
                            'action'     => 'find-committee'
                        ]
                    ]
                ],
                'president' => [
                    'options' => [
                        'route'    => 'load:president',
                        'defaults' => [
                            'controller' => 'AlthingiAggregator\Controller\President',
                            'action'     => 'find-president'
                        ]
                    ]
                ],
            ],
        ],
    ],
);
