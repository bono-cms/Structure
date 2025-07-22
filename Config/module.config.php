<?php

/**
 * Module configuration container
 */

return [
    'perPageCount' => 10,
    'caption'  => 'Structure',
    'description' => 'Create any data structures and display them anywhere on your web-site',
    'menu' => [
        'name'  => 'Data structures',
        'icon' => 'fas fa-table',
        'items' => [
            [
                'route' => 'Structure:Admin:Dashboard@indexAction',
                'name' => 'Manage data structures'
            ]
        ]
    ]
];