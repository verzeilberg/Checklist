<?php

namespace CheckList;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'checklist' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/checklist[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'checklistajax' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/checklistajax[/:action]',
                    'defaults' => [
                        'controller' => 'checklistajax'
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\CheckListController::class => Controller\Factory\CheckListControllerFactory::class,
            Controller\CheckListItemController::class => Controller\Factory\CheckListItemControllerFactory::class,
            Controller\CheckListAjaxController::class => Controller\Factory\CheckListAjaxControllerFactory::class,
        ],
        'aliases' => [
            'checklistbeheer' => Controller\CheckListController::class,
            'checklistitembeheer' => Controller\CheckListItemController::class,
            'checklistajax' => Controller\CheckListAjaxController::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view', 
        ],
    ],
    'service_manager' => [
        'invokables' => [
            CheckList\checkListServiceInterface::class => Service\checkListService::class,
            CheckList\checkListFieldServiceInterface::class => Service\checkListFieldService::class,
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            'checklistbeheer' => [
                // to anyone.
                ['actions' => '*', 'allow' => '+checklist.manage']
            ],
            'checklistitembeheer' => [
                // to anyone.
                ['actions' => '*', 'allow' => '+checklist.manage']
            ],
            'checklistajax' => [
                // to anyone.
                ['actions' => '*', 'allow' => '+checklist.manage']
            ],
        ]
    ],
];
