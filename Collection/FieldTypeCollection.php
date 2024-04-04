<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Structure\Collection;

use Krystal\Stdlib\ArrayCollection;
use Krystal\Filesystem\FileManager;

final class FieldTypeCollection extends ArrayCollection
{
    /* Text fields */
    const FIELD_TEXT = 1;
    const FIELD_TEXTAREA = 2;
    const FIELD_WYSIWYG = 3;
    const FIELD_NUMBER = 4;
    const FIELD_EMAIL = 5;

    /* Date and time */
    const FIELD_DATE = 6;
    const FIELD_TIME = 7;
    const FIELD_DATETIME = 8;

    /* Lists */
    const FIELD_SELECT = 9;
    const FIELD_RADIO = 10;
    const FIELD_DATALIST = 11;

    /* Other */
    const FIELD_CHECKBOX = 12;

    /* Files */
    const FIELD_FILE = 13;
    const FIELD_IMAGE = 14;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        'Text fields' => [
            self::FIELD_TEXT => 'Text',
            self::FIELD_TEXTAREA => 'Textarea',
            self::FIELD_WYSIWYG => 'Rich editor (WYSIWYG)',
            self::FIELD_NUMBER => 'Number',
            self::FIELD_EMAIL => 'E-mail'
        ],
        'Date and time' => [
            self::FIELD_DATE => 'Date',
            self::FIELD_TIME => 'Time',
            self::FIELD_DATETIME => 'Date and time',
        ],
        'Lists' => [
            self::FIELD_SELECT => 'Select',
            self::FIELD_RADIO => 'Radios',
            self::FIELD_DATALIST => 'Data list'
        ],
        'Other' => [
            self::FIELD_CHECKBOX => 'Checkbox',
        ],
        'Files' => [
            self::FIELD_FILE => 'File',
            self::FIELD_IMAGE => 'Image'
        ]
    ];

    /**
     * Accept map for file input
     * 
     * @var array
     */
    protected static $accept = [
        self::FIELD_IMAGE => 'image/apng, image/avif, image/gif, image/jpeg, image/png, image/svg+xml, image/webp'
    ];

    /**
     * Returns accept type by field constant
     * 
     * @param int $type Filed type constant
     * @return mixed
     */
    public static function getAccept($type)
    {
        if (isset(self::$accept[$type])) {
            return self::$accept[$type];
        } else {
            return null;
        }
    }

    /**
     * Whether this is text field
     * 
     * @param int $type
     * @return boolean
     */
    public static function isText($type)
    {
        return in_array($type, [
            self::FIELD_TEXT,
            self::FIELD_TEXTAREA,
            self::FIELD_WYSIWYG,
            self::FIELD_NUMBER,
            self::FIELD_EMAIL
        ]);
    }

    /**
     * Whether this is datetime
     * 
     * @param int $type
     * @return boolean
     */
    public static function isDatetime($type)
    {
        return in_array($type, [
            self::FIELD_DATE,
            self::FIELD_TIME,
            self::FIELD_DATETIME
        ]);
    }

    /**
     * Whether this is datetime
     * 
     * @param int $type
     * @return boolean
     */
    public static function isList($type)
    {
        return in_array($type, [
            self::FIELD_SELECT,
            self::FIELD_RADIO,
            self::FIELD_DATALIST
        ]);
    }

    /**
     * Whether this is other type
     * 
     * @param int $type
     * @return boolean
     */
    public static function isOther($type)
    {
        return in_array($type, [
            self::FIELD_CHECKBOX
        ]);
    }

    /**
     * Whether this is static field
     * 
     * @param int $type
     * @return boolean
     */
    public static function isFile($type)
    {
        return in_array($type, [
            self::FIELD_FILE,
            self::FIELD_IMAGE
        ]);
    }

    /**
     * Whether a path to a file looks like an image
     * 
     * @param string $path
     * @return boolean
     */
    public static function imageLike($file)
    {
        return FileManager::hasExtension($file, [
            'jpg',
            'jpeg',
            'gif',
            'png',
            'svg',
            'avif',
            'webp'
        ]);
    }
}
