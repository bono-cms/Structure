<?php

return [
    // Fields
    '/%s/module/structure/view/(:var)' => [
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
    '/%s/module/structure' => [
        'controller' => 'Admin:Collection@indexAction'
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