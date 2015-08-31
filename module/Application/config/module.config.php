<?php
namespace Application;

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
            'change_language' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/change-language/:lang',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'change-language',
                        'lang' => 'el'
                    ),
                ),
            ),
            'about' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/about',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\About',
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
                                'action' => 'remove'
                            )
                        )
                    )
                )
            ),
            'contents' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/contents',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Content',
                        'action' => 'list',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'save' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/save',
                            'defaults' => array(
                                'action' => 'save'
                            )
                        )
                    ),
                )
            ),
            'settings' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/settings',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Setting',
                        'action' => 'list',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'save' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/save',
                            'defaults' => array(
                                'action' => 'save'
                            )
                        )
                    ),
                )
            ),
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'home',
                    ),
                ),
            ),
            'sitemap_direct' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/sitemap.xml',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'sitemap',
                    ),
                ),
            ),
            'sitemap' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/sitemap[/:type[/:index]]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'sitemap',
                    ),
                ),
            ),
            'upload-file' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/upload-file',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'upload-file'
                    )
                )
            ),
            'download-file' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/download/:fileName',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'download-file'
                    )
                )
            ),
            'about_index' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/about',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'about',
                    ),
                ),
            ),
            'contact' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/contact',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'contact',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'contactForm' => __NAMESPACE__ . '\Factory\ContactFormFactory',
            'settingForm' => __NAMESPACE__ . '\Factory\SettingFormFactory',
            'Zend\Session\SessionManager' => __NAMESPACE__ . '\Factory\SessionManagerFactory',
        ),
        'invokables' => array(
            'contentService' => __NAMESPACE__ . '\Service\ContentService',
            'settingService' => __NAMESPACE__ . '\Service\SettingService',
            'fileUtilService' => __NAMESPACE__ . '\Service\FileUtilService',
            'contentFilter' => __NAMESPACE__ . '\Filter\ContentFilter',
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'translate' => __NAMESPACE__ . '\Factory\TranslatePluginFactory',
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'routeName' => __NAMESPACE__ . '\Factory\ActionNameHelperFactory',
            'showMessages' => __NAMESPACE__ . '\Factory\ShowMessagesHelperFactory',
            'config' => __NAMESPACE__ . '\Factory\ConfigHelperFactory',
            'getContent' => __NAMESPACE__ . '\Factory\GetContentHelperFactory'
        ),
        'invokables' => array(
            'mobile' => __NAMESPACE__ . '\View\Helper\Mobile',
            'joinSelect' => __NAMESPACE__ . '\View\Helper\JoinSelect',
            'select' => __NAMESPACE__ . '\View\Helper\Select',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\About' => 'Application\Controller\AboutController',
            'Application\Controller\Setting' => 'Application\Controller\SettingController',
            'Application\Controller\Content' => 'Application\Controller\ContentController'
        ),
        'initializers' => array(
            'entityManager' => __NAMESPACE__ . '\Factory\EntityManagerInitializer',
            'vocabulary' => __NAMESPACE__ . '\Factory\VocabularyInitializer'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/admin' => __DIR__ . '/../view/layout/admin-layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'header' => __DIR__ . '/../view/partial/header.phtml',
            'header_admin' => __DIR__ . '/../view/partial/header-admin.phtml',
            'footer' => __DIR__ . '/../view/partial/footer.phtml',
            'admin_paginator' => __DIR__ . '/../view/partial/paginator.phtml',
            'table_options' => __DIR__ . '/../view/partial/table_options.phtml',
            'footer_admin' => __DIR__ . '/../view/partial/footer-admin.phtml',
            'info' => __DIR__ . '/../view/partial/info.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
