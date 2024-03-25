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
    const FIELD_CHECKBOX = 10;
    const FIELD_RADIO = 11;
    const FIELD_DATALIST = 12;

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
            //self::FIELD_CHECKBOX => 'Checkboxes',
            self::FIELD_RADIO => 'Radios',
            self::FIELD_DATALIST => 'Data list'
        ]
    ];

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
            self::FIELD_CHECKBOX,
            self::FIELD_RADIO,
            self::FIELD_DATALIST
        ]);
    }
}
