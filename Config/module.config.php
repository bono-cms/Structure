<?php

/**
 * Module configuration container
 */

return [
    'caption'  => 'Strucutre',
    'description' => 'Create any data structures and display them on your web-site',
    'menu' => [
        'name'  => 'Data strucutres',
        'icon' => 'fas fa-phone-square',
        'items' => [
            [
                'route' => 'Strucutre:Admin:Strucutre@indexAction',
                'name' => 'Manage data structures'
            ]
        ]
    ]
];