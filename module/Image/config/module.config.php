<?php
namespace Image;

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
            'galleries_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/galleries[/:galleryName]',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Gallery',
                        'action' => 'index',
                    ),
                ),
            ),
//            'posts_index' => array(
//                'type' => 'Zend\Mvc\Router\Http\Segment',
//                'options' => array(
//                    'route' => '/news[/:page]',
//                    'defaults' => array(
//                        'controller' => __NAMESPACE__ . '\Controller\Index',
//                        'action' => 'index',
//                        'page' => 1
//                    ),
//                    'constraints' => array(
//                        'page' => '[0-9]+'
//                    )
//                ),
//            ),
            'galleries' =>   array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/galleries',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Gallery',
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
            'images' =>   array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/images',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Image',
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
            'galleryForm' => __NAMESPACE__ . '\Factory\GalleryFormFactory',
            'imageForm' => __NAMESPACE__ . '\Factory\ImageFormFactory',
        ),
        'invokables' => array(
            'galleryService' => __NAMESPACE__ . '\Service\GalleryService',
            'galleryFilter' => __NAMESPACE__ . '\Filter\GalleryFilter',
            'imageService' => __NAMESPACE__ . '\Service\ImageService',
            'imageFilter' => __NAMESPACE__ . '\Filter\ImageFilter',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            __NAMESPACE__ . '\Controller\Image' => __NAMESPACE__ . '\Controller\ImageController',
            __NAMESPACE__ . '\Controller\Gallery' => __NAMESPACE__ . '\Controller\GalleryController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'gallery' => __DIR__ . '/../view/partial/gallery.phtml',
            'image' => __DIR__ . '/../view/partial/image.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
