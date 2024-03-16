<?php

return [
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