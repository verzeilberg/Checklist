<?php

namespace CheckList;

use CheckList\Entity\CheckList;
use CheckList\Form\CreateChecklistFieldForm;
use CheckList\Form\CreateChecklistFieldFormFactory;
use CheckList\Form\CreateChecklistForm;
use CheckList\Form\CreateChecklistFormFactory;
use CheckList\Form\Element\ParentSelect;
use CheckList\Form\Element\ParentSelectFactory;
use Laminas\Form\Factory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\ServiceManager\Factory\InvokableFactory;

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
    'view_helpers' => [
        'factories' => [
            View\Helper\CheckListHelper::class => View\Helper\Factory\CheckListHelperFactory::class,
        ],
        'aliases' => [
            'checkListHelper' => View\Helper\CheckListHelper::class,
        ],
    ],
    'service_manager' => [
        'invokables' => [
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
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../public',
            ],
        ],
    ],
];
