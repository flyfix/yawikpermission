<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2016 Cross Solution (http://cross-solution.de)
 * @author        cbleek
 * @license       MIT
 */
return [
    'navigation' => [
        'default' => [
            'admin' => [
                'pages' => [
                    'users' => [
                        'label'    =>  /*@translate*/ 'Users',
                        'route' => 'lang/user-list',
                        'order' => '100',
                        'resource' => 'Users',
                    ]
                ]
            ]
        ]
    ]
];
