<?php

/**
 * Module configuration container
 */

return [
    'caption'  => 'Structure',
    'description' => 'Create any data structures and display them anywhere on your web-site',
    'menu' => [
        'name'  => 'Data strucutres',
        'icon' => 'fas fa-table',
        'items' => [
            [
                'route' => 'Structure:Admin:Dashboard@indexAction',
                'name' => 'Manage data structures'
            ]
        ]
    ]
];