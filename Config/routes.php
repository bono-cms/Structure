<?php

return [
    // API
    '/module/structure/index' => [
        'controller' => 'API@indexAction'
    ],

    '/%s/module/structure/flush' => [
        'controller' => 'Admin:Dashboard@flushAction'
    ],
    
    '/%s/module/structure' => [
        'controller' => 'Admin:Dashboard@indexAction'
    ],
    // Repeater
    '/%s/module/structure/repeater/view/(:var)/' => [
        'controller' => 'Admin:Repeater@indexAction'
    ],

    '/%s/module/structure/repeater/delete/(:var)' => [
        'controller' => 'Admin:Repeater@deleteAction'
    ],

    '/%s/module/structure/repeater/edit/(:var)/(:var)' => [
        'controller' => 'Admin:Repeater@editAction'
    ],

    '/%s/module/structure/repeater/save' => [
        'controller' => 'Admin:Repeater@saveAction'
    ],

    // Fields
    '/%s/module/structure/fields/view/(:var)' => [
        'controller' => 'Admin:Field@indexAction'
    ],

    '/%s/module/structure/fields/save' => [
        'controller' => 'Admin:Field@saveAction'
    ],

    '/%s/module/structure/fields/delete/(:var)' => [
        'controller' => 'Admin:Field@deleteAction'
    ],

    '/%s/module/structure/fields/edit/(:var)' => [
        'controller' => 'Admin:Field@editAction'
    ],

    // Collections
    '/%s/module/structure/collection' => [
        'controller' => 'Admin:Collection@indexAction'
    ],

    '/%s/module/structure/collection/truncate/(:var)' => [
        'controller' => 'Admin:Collection@truncateAction'
    ],

    '/%s/module/structure/collection/delete/(:var)' => [
        'controller' => 'Admin:Collection@deleteAction'
    ],

    '/%s/module/structure/collection/edit/(:var)' => [
        'controller' => 'Admin:Collection@editAction'
    ],

    '/%s/module/structure/collection/add' => [
        'controller' => 'Admin:Collection@addAction'
    ],

    '/%s/module/structure/collection/save' => [
        'controller' => 'Admin:Collection@saveAction'
    ]
];