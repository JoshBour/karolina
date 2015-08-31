<?php
namespace Post;

return array(
    'doctrine' => array(
        'driver' => array(
            'entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => 'entity',
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'post_view' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/posts/:postUrl',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Post',
                        'action' => 'view',
                    ),
                ),
            ),
            'posts_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/posts[/:page]',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Post',
                        'action' => 'index',
                        'page' => 1
                    ),
                    'constraints' => array(
                        'page' => '[0-9]+'
                    )
                ),
            ),
            'posts' =>   array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/posts',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Post',
                        'action' => 'list',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'load' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/load[?page=:page&limit=:limit&search=:search&sort=:sort]',
                            'defaults' => array(
                                'action' => 'load'
                            )
                        )
                    ),
                    'save' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/save',
                            'defaults' => array(
                                'action' => 'save'
                            )
                        )
                    ),
                    'add' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add'
                            )
                        )
                    ),
                    'delete' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/delete',
                            'defaults' => array(
                                'action' => 'delete'
                            )
                        )
                    )
                )
            ),
            'services_index' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/services',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Service',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'view' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/:serviceUrl',
                            'defaults' => array(
                                'action' => 'view'
                            )
                        )
                    ),
                ),
            ),
            'services' =>   array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/services',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Service',
                        'action' => 'list',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'load' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/load[?page=:page&limit=:limit&search=:search&sort=:sort]',
                            'defaults' => array(
                                'action' => 'load'
                            )
                        )
                    ),
                    'save' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/save',
                            'defaults' => array(
                                'action' => 'save'
                            )
                        )
                    ),
                    'add' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add'
                            )
                        )
                    ),
                    'delete' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/delete',
                            'defaults' => array(
                                'action' => 'delete'
                            )
                        )
                    )
                )
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'postForm' => __NAMESPACE__ . '\Factory\PostFormFactory',
            'serviceForm' => __NAMESPACE__ . '\Factory\ServiceFormFactory',
        ),
        'invokables' => array(
            'postService' => __NAMESPACE__ . '\Service\PostService',
            'postFilter' => __NAMESPACE__ . '\Filter\PostFilter',
            'serviceService' => __NAMESPACE__ . '\Service\ServiceService',
            'serviceFilter' => __NAMESPACE__ . '\Filter\ServiceFilter',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            __NAMESPACE__ . '\Controller\Post' => __NAMESPACE__ . '\Controller\PostController',
            __NAMESPACE__ . '\Controller\Service' => __NAMESPACE__ . '\Controller\ServiceController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'paginator' => __DIR__ . '/../view/partial/paginator.phtml',
            'post' => __DIR__ . '/../view/partial/post.phtml',
            'service' => __DIR__ . '/../view/partial/service.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
