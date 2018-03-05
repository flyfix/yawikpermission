<?php
/**
 * YAWIK
 * Configuration file of the Core module
 *
 * This file intents to provide the configuration for all other modules
 * as well (convention over configuration).
 * Having said that, you may always overwrite or extend the configuration
 * in your own modules configuration file(s) (or via the config autoloading).
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */
namespace Core;

use Core\Factory\Controller\AdminControllerFactory;
use Core\Factory\Controller\FileControllerFactory;
use Core\Factory\Controller\LazyControllerFactory;
use Zend\I18n\Translator\Resources;

$doctrineConfig = include __DIR__ . '/doctrine.config.php';


return array(

    'doctrine' => $doctrineConfig,

    'options' => [
        'Core/MailServiceOptions' => [ 'class' => '\Core\Options\MailServiceOptions' ],
        ],
    
    'Core' => array(
        'settings' => array(
            'entity' => '\\Core\\Entity\\SettingsContainer',
            'navigation_label' => /* @translate */ 'general settings',
            'navigation_class' => 'yk-icon yk-icon-settings'
        ),
    ),


    // Logging
    'log' => array(
        'Core/Log' => array(
            'writers' => array(
                 array(
                     'name' => 'stream',
                    'priority' => 1000,
                    'options' => array(
                         'stream' => __DIR__ .'/../../../log/yawik.log',
                    ),
                 ),
            ),
        ),
        'Log/Core/Mail' => array(
            'writers' => array(
                 array(
                     'name' => 'stream',
                    'priority' => 1000,
                    'options' => array(
                         'stream' => __DIR__ .'/../../../log/mails.log',
                    ),
                 ),
            ),
        ),
    ),

    'log_processors' => [
        'invokables' => [
            'Core/UniqueId' => 'Core\Log\Processor\UniqueId',
        ],
    ],
    
    'tracy' => [
        'mode' => true, // true = production|false = development|null = autodetect|IP address(es) csv/array
        'bar' => false, // bool = enabled|Toggle nette diagnostics bar.
        'strict' => true, // bool = cause immediate death|int = matched against error severity
        'log' => __DIR__ . '/../../../log/tracy', // path to log directory (this directory keeps error.log, snoozing mailsent file & html exception trace files)
        'email' => null, // in production mode notifies the recipient
        'email_snooze' => 900 // interval for sending email in seconds
    ],

    // Routes
    'router' => array(
        'routes' => array(
            'lang' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:lang',
                    'defaults' => array(
                        'controller' => 'Core/Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'admin' => array(
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/admin',
                            'defaults' => [
                                'controller' => 'Core/Admin',
                                'action' => 'index'
                            ],
                        ],
                        'may_terminate' => true,
                    ),
                    'error' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/error',
                            'defaults' => array(
                                'controller' => 'Core\Controller\Index',
                                'action' => 'error',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'mailtest' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/mail',
                            'defaults' => array(
                                'controller' => 'Core\Controller\Index',
                                'action' => 'mail',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'content' => array(
                        'type' => 'Regex',
                        'options' => array(
                            'regex' => '/content/(?<view>.*)$',
                            'defaults' => array(
                                'controller' => 'Core/Content',
                                'action' => 'index',
                            ),
                            'spec' => '/content/%view%'
                        ),
                        'may_terminate' => true,

                    ),
                    'dashboard' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/dashboard',
                            'defaults' => [
                                'controller' => 'Core/Index',
                                'action' => 'dashboard'
                            ]
                        ]
                    ]
                ),
            ),
            'file' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/file/:filestore/:fileId[/:fileName]',
                    'defaults' => array(
                        'controller' => 'Core/File',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    
    'acl' => array(
        'rules' => array(
            'guest' => array(
                'allow' => array(
                    //'route/file',
                    'Entity/File' => array(
                        '__ALL__' => 'Core/FileAccess'
                    ),
                ),
                'disallow' => [

                ]
            ),
            'admin' => [
                'allow' => [
                    'route/lang/admin',
                    //'Core/Navigation/Admin',
                ],
            ],
            'recruiter' => [
                'allow' => [
                    'route/lang/dashboard'
                ]
            ]
        ),
        'assertions' => array(
            'factories' => array(
                \Core\Acl\FileAccessAssertion::class => \Zend\ServiceManager\Factory\InvokableFactory::class
            ),
            'aliases' =>  [
                'Core/FileAccess' => \Core\Acl\FileAccessAssertion::class,
            ],
        ),
    ),
    
    // Setup the service manager
    'service_manager' => array(
        'invokables' => array(
            'Notification/Event'         => 'Core\Listener\Events\NotificationEvent',
            'Core/EventManager'          => 'Core\EventManager\EventManager',
            'Core/Options/ImagineOptions'             => \Core\Options\ImagineOptions::class,
        ),
        'factories' => array(
            'configaccess' => 'Core\Service\Config::factory',
            'templateProvider' => 'Core\Service\TemplateProvider::factory',
            'Core/DocumentManager' => \Core\Repository\DoctrineMongoODM\DocumentManagerFactory::class,
            'Core/RepositoryService' => 'Core\Repository\RepositoryServiceFactory',
            'Core/MailService' => '\Core\Mail\MailServiceFactory',
            'Core/PaginatorService' => '\Core\Paginator\PaginatorServiceFactory',
            'Core/Html2Pdf' => '\Core\Html2Pdf\PdfServiceFactory',
            'Core/Navigation' => 'Core\Factory\Navigation\DefaultNavigationFactory',
            'Core/JsonEntityHydrator' => 'Core\Entity\Hydrator\JsonEntityHydratorFactory',
            'Core/EntityHydrator' => 'Core\Entity\Hydrator\EntityHydratorFactory',
            'Core/Options' => 'Core\Factory\ModuleOptionsFactory',
            'Core/DoctrineMongoODM/RepositoryEvents' => [\Core\Repository\DoctrineMongoODM\Event\RepositoryEventsSubscriber::class,'factory'],
            'DefaultListeners' => ['Core\Listener\DefaultListener','factory'],
            'templateProviderStrategy'   => ['Core\Form\Hydrator\Strategy\TemplateProviderStrategy','factory'],
            'Core/Listener/DeferredListenerAggregate' => [\Core\Listener\DeferredListenerAggregate::class,'factory'],
            'Core/Listener/CreatePaginator' => 'Core\Listener\CreatePaginatorListener::factory',
            'Core/Locale' => 'Core\I18n\LocaleFactory',
            \Core\Listener\AjaxRouteListener::class => \Core\Factory\Listener\AjaxRouteListenerFactory::class,
            \Core\Listener\DeleteImageSetListener::class => \Core\Factory\Listener\DeleteImageSetListenerFactory::class,
            'Imagine' => \Core\Factory\Service\ImagineFactory::class,
            'Core/Listener/Notification' => [\Core\Listener\NotificationListener::class,'factory'],
        ),
        'abstract_factories' => array(
            'Core\Factory\OptionsAbstractFactory',
            'Core\Factory\EventManager\EventManagerAbstractFactory',
        ),
        'aliases' => array(
            'forms' => 'FormElementManager',
            'repositories' => 'Core/RepositoryService',
            'mvctranslator' => 'MvcTranslator',
            'translator' => 'MvcTranslator',
        ),
        'shared' => array(
            'Core/Listener/DeferredListenerAggregate' => false,
        ),
    ),

    // Translation settings consumed by the 'translator' factory above.
    'translator' => array(
        'locale' => 'en_EN',
        'translation_file_patterns' => array(
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
            [
                'type'     => 'phparray',
                'base_dir' => Resources::getBasePath(),
                'pattern'  => Resources::getPatternForValidator(),
            ],
            [
                'type'     => 'phparray',
                'base_dir' => Resources::getBasePath(),
                'pattern' => Resources::getPatternForCaptcha(),
            ]
        ),
    ),
    // Defines the Core/Navigation.
    'navigation' => array(
        'default' => array(
            'home' => [
                'label' => /*@translate*/ 'Home',
                'route' => 'lang',
                'visible' => false
            ],
            'dashboard' => [
                'label' => /*@translate*/ 'Dashboard',
                'route' => 'lang/dashboard',
                'resource' => 'route/lang/dashboard',
                'order' => -10
            ],
            'admin' => [
                'label ' => /*@translate*/ 'Admin',
                'route' => 'lang/admin',
                'resource' => 'route/lang/admin',
                'order' => 200,
            ],
        ),
    ),
    // Configuration of the controller service manager (Which loads controllers)
    'controllers' => array(
	    'factories' => [
		    'Core/Index'   => LazyControllerFactory::class,
            'Core/Admin'   => AdminControllerFactory::class,
		    'Core/File'    => FileControllerFactory::class,
            'Core/Content' => LazyControllerFactory::class,
	    ],
    ),
    // Configuration of the controller plugin service manager
    'controller_plugins' => array(
        'factories' => array(
            'config' => 'Core\Controller\Plugin\ConfigFactory',
            'Notification' => '\Core\Controller\Plugin\Service\NotificationFactory',
            'entitysnapshot' => 'Core\Controller\Plugin\Service\EntitySnapshotFactory',
            'Core/SearchForm' => 'Core\Factory\Controller\Plugin\SearchFormFactory',
            'listquery' => 'Core\Controller\Plugin\ListQuery::factory',
            'mail' => 'Core\Controller\Plugin\Mail::factory',
            'Core/Mailer' => ['Core\Controller\Plugin\Mailer','factory'],
            'Core/CreatePaginator' => [\Core\Controller\Plugin\CreatePaginator::class,'factory'],
            'Core/PaginatorService' => [\Core\Controller\Plugin\CreatePaginatorService::class,'factory'],
        ),
        'invokables' => array(
            'Core/FileSender' => 'Core\Controller\Plugin\FileSender',
            'Core/ContentCollector' => 'Core\Controller\Plugin\ContentCollector',
            'Core/PaginationParams' => 'Core\Controller\Plugin\PaginationParams',
            'Core/PaginationBuilder' => 'Core\Controller\Plugin\PaginationBuilder',
        ),
        'aliases' => array(
            'filesender'       => 'Core/FileSender',
            'mailer'           => 'Core/Mailer',
            'Mailer'           => 'Core/Mailer',
            'pagination'       => 'Core/PaginationBuilder',
            'paginator'        => 'Core/CreatePaginator',
            'paginatorservice' => 'Core/PaginatorService',
            'paginationParams' => 'Core/PaginationParams',
            'searchform'       => 'Core/SearchForm',
	        'notification'     => 'Notification',
        )
    ),
    // Configure the view service manager
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'unauthorized_template' => 'error/403',
        'exception_template' => 'error/index',
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
            'noscript-notice' => __DIR__ . '/../view/layout/_noscript-notice.phtml',
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/403' => __DIR__ . '/../view/error/403.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'main-navigation' => __DIR__ . '/../view/partial/main-navigation.phtml',
            'pagination-control' => __DIR__ . '/../view/partial/pagination-control.phtml',
            'core/loading-popup' => __DIR__ . '/../view/partial/loading-popup.phtml',
            'core/notifications' => __DIR__ . '/../view/partial/notifications.phtml',
            'form/core/buttons' => __DIR__ . '/../view/form/buttons.phtml',
            'core/social-buttons' => __DIR__ . '/../view/partial/social-buttons.phtml',
            'form/core/privacy' => __DIR__ . '/../view/form/privacy.phtml',
            'core/form/permissions-fieldset' => __DIR__ . '/../view/form/permissions-fieldset.phtml',
            'core/form/permissions-collection' => __DIR__ . '/../view/form/permissions-collection.phtml',
            'core/form/container-view' => __DIR__ . '/../view/form/container.view.phtml',
            'core/form/tree-manage.view' => __DIR__ . '/../view/form/tree-manage.view.phtml',
            'core/form/tree-manage.form' => __DIR__ . '/../view/form/tree-manage.form.phtml',
            'core/form/tree-add-item' => __DIR__ . '/../view/form/tree-add-item.phtml',
            'mail/header' =>  __DIR__ . '/../view/mail/header.phtml',
            'mail/footer' =>  __DIR__ . '/../view/mail/footer.phtml',
            'mail/footer.en' =>  __DIR__ . '/../view/mail/footer.en.phtml',
            //'startpage' => __DIR__ . '/../view/layout/startpage.phtml',
        ),
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'formElement' => 'Core\Form\View\Helper\FormElement',
            'formLabel'  => 'Core\Form\View\Helper\RequiredMarkInFormLabel',
            'form' => 'Core\Form\View\Helper\Form',
            'formSimple' => 'Core\Form\View\Helper\FormSimple',
            'formContainer' => 'Core\Form\View\Helper\FormContainer',
            'formWizardContainer' => 'Core\Form\View\Helper\FormWizardContainer',
            'formCollectionContainer' => 'Core\Form\View\Helper\FormCollectionContainer',
            'summaryForm' => 'Core\Form\View\Helper\SummaryForm',
            'searchForm' => 'Core\Form\View\Helper\SearchForm',
            'filterForm' => 'Core\Form\View\Helper\FilterForm',
            'formPartial' => '\Core\Form\View\Helper\FormPartial',
            'formCollection' => 'Core\Form\View\Helper\FormCollection',
            'formRow' => 'Core\Form\View\Helper\FormRow',
            'formRowSimple' => 'Core\Form\View\Helper\FormSimpleRow',
            'formRowCombined' => 'Core\Form\View\Helper\FormRowCombined',
            'formFileUpload' => 'Core\Form\View\Helper\FormFileUpload',
            'formImageUpload' => 'Core\Form\View\Helper\FormImageUpload',
            'formimageupload' => 'Core\Form\View\Helper\FormImageUpload',
            
            /* @TODO: [ZF3] make this setting to be camel cased */
            'formCheckBox' => 'Core\Form\View\Helper\FormCheckbox',
            'formcheckbox' => 'Core\Form\View\Helper\FormCheckbox',
            
            'formDatePicker' => 'Core\Form\View\Helper\FormDatePicker',
            'formInfoCheckBox' => 'Core\Form\View\Helper\FormInfoCheckbox',
            'formSelect' => 'Core\Form\View\Helper\FormSelect',
            'dateFormat' => 'Core\View\Helper\DateFormat',
            'salutation' => 'Core\View\Helper\Salutation',
            'period' => 'Core\View\Helper\Period',
            'link'   => 'Core\View\Helper\Link',
            'languageSwitcher' => 'Core\View\Helper\LanguageSwitcher',
            'rating' => 'Core\View\Helper\Rating',
            'base64' => 'Core\View\Helper\Base64',
            'alert' => 'Core\View\Helper\Alert',
            'spinnerButton' => 'Core\Form\View\Helper\Element\SpinnerButton',
            'toggleButton' => 'Core\Form\View\Helper\ToggleButton',
            'TinyMCEditor' => 'Core\Form\View\Helper\FormEditor',
            'TinyMCEditorColor' => 'Core\Form\View\Helper\FormEditorColor',
        ),
        'factories' => array(
            'params' => 'Core\View\Helper\Service\ParamsHelperFactory',
            'socialButtons' => 'Core\Factory\View\Helper\SocialButtonsFactory',
            'TinyMCEditorLight' => 'Core\Factory\Form\View\Helper\FormEditorLightFactory',
            'configHeadScript' => 'Core\View\Helper\Service\HeadScriptFactory',
            \Core\View\Helper\AjaxUrl::class => \Core\Factory\View\Helper\AjaxUrlFactory::class,
            'services' => [\Core\View\Helper\Services::class, 'factory'],
            'InsertFile' => [View\Helper\InsertFile::class, 'factory'],
            \Core\View\Helper\Snippet::class => \Core\Factory\View\Helper\SnippetFactory::class,
            \Core\View\Helper\Proxy::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ),
        'initializers' => array(
//            '\Core\View\Helper\Service\HeadScriptInitializer',
        ),
        'aliases' => [
            'snippet' => \Core\View\Helper\Snippet::class,
	        'ajaxUrl' => \Core\View\Helper\AjaxUrl::class,
            'proxy' => \Core\View\Helper\Proxy::class,
            'form_element' => 'formElement',
        ],
    ),
    
    'view_helper_config' => array(
        'flashmessenger' => array(
            'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
            'message_separator_string' => '</li><li>',
            'message_close_string'     => '</li></ul></div>',
        ),
        'form_editor' => [
            'light' => [
                'toolbar' => 'undo redo | formatselect | alignleft aligncenter alignright ',
                'block_formats' => 'Job title=h1;Subtitle=h2'
                ]
        ]
    ),
    
    'filters' => array(
        'invokables' => array(
            'Core/Repository/PropertyToKeywords' => 'Core\Repository\Filter\PropertyToKeywords',
        ),
        'factories' => array(
            "Core/XssFilter" => 'Core\Filter\XssFilterFactory',
            "Core/HtmlAbsPathFilter" => 'Core\Factory\Filter\HtmlAbsPathFilterFactory',
        ),
    ),
    
    'form_elements' => array(
        'invokables' => array(
            'DefaultButtonsFieldset' => '\Core\Form\DefaultButtonsFieldset',
            'FormSubmitButtonsFieldset' => '\Core\Form\FormSubmitButtonsFieldset',
            'SummaryFormButtonsFieldset' => 'Core\Form\SummaryFormButtonsFieldset',
            'Checkbox' => 'Core\Form\Element\Checkbox',
            'infoCheckBox' => 'Core\Form\Element\InfoCheckbox',
            'Core/ListFilterButtons' => '\Core\Form\ListFilterButtonsFieldset',
            'Core/Datepicker' => 'Core\Form\Element\DatePicker',
            'Core/FileUpload' => '\Core\Form\Element\FileUpload',
            'Core\FileCollection' => 'Core\Form\FileCollection',
            'Core/LocalizationSettingsFieldset' => 'Core\Form\LocalizationSettingsFieldset',
            'Core/RatingFieldset' => 'Core\Form\RatingFieldset',
            'Core/Rating' => 'Core\Form\Element\Rating',
            'Core/PermissionsFieldset' => 'Core\Form\PermissionsFieldset',
            'Core/PermissionsCollection' => 'Core\Form\PermissionsCollection',
            'Location' => 'Zend\Form\Element\Text',
            'Core/Spinner-Submit' => 'Core\Form\Element\SpinnerSubmit',
            'ToggleButton' => 'Core\Form\Element\ToggleButton',
            'TextEditor' => 'Core\Form\Element\Editor',
            'TextEditorLight' => 'Core\Form\Element\EditorLight',
            'Core/Container' => 'Core\Form\Container',
            'Core/Tree/Management' => 'Core\Form\Tree\ManagementForm',
            'Core/Tree/ManagementFieldset' => 'Core\Form\Tree\ManagementFieldset',
            'Core/Tree/AddItemFieldset' => 'Core\Form\Tree\AddItemFieldset',
            'Core/Search' => 'Core\Form\SearchForm',
        ),
        'factories' => [
            'Core/Tree/Select' => 'Core\Factory\Form\Tree\SelectFactory',
        ],
        'initializers' => array(
            '\Core\Form\Service\InjectHeadscriptInitializer',
            '\Core\Form\Service\Initializer',
        ),
        'aliases' => array(
            'submitField' => 'FormSubmitButtonsFieldset'
        ),
    ),

    'paginator_manager' => [
        'abstract_factories' => [
            '\Core\Factory\Paginator\RepositoryAbstractFactory',
        ],
    ],
    
    'mails_config' => array(
        'from' => array(
            'email' => 'no-reply@host.tld',
            'name'  => 'YAWIK'
        ),
    ),

    'event_manager' => [
        'Core/AdminController/Events' => [
            'service' => 'Core/EventManager',
            'event' => '\Core\Controller\AdminControllerEvent',
        ],

        'Core/CreatePaginator/Events' => [
            'service' => 'Core/EventManager',
            'event' => '\Core\Listener\Events\CreatePaginatorEvent'
        ],

        'Core/ViewSnippets/Events' => [
            'service' => 'Core/EventManager',
        ],

        'Core/Ajax/Events' => [
	        'service' => 'Core/EventManager',
	        'event'   => \Core\Listener\Events\AjaxEvent::class,
        ],
	    
	    'Core/File/Events' => [
		    'service' => 'Core/EventManager',
		    'event' => \Core\Listener\Events\FileEvent::class,
            'listeners' => [
                \Core\Listener\DeleteImageSetListener::class => [\Core\Listener\Events\FileEvent::EVENT_DELETE, -1000],
            ],
	    ]
    ],
    
);
