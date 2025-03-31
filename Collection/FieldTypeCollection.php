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
    const FIELD_URL = 18;
    const FIELD_ARRAY = 20;

    /* Date and time */
    const FIELD_DATE = 6;
    const FIELD_TIME = 7;
    const FIELD_DATETIME = 8;
    const FIELD_WEEK = 16;
    const FIELD_MONTH = 17;

    /* Lists */
    const FIELD_SELECT = 9;
    const FIELD_RADIO = 10;
    const FIELD_DATALIST = 11;

    /* Other */
    const FIELD_CHECKBOX = 12;
    const FIELD_COLOR = 19;

    /* Files */
    const FIELD_FILE = 13;
    const FIELD_IMAGE = 14;
    const FIELD_PDF = 15;
    const FIELD_ARCHIVE = 21;
    const FIELD_AUDIO = 22;
    const FIELD_VIDEO = 23;
    const FIELD_GRAPHIC = 24;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        'Text fields' => [
            self::FIELD_TEXT => 'Text',
            self::FIELD_TEXTAREA => 'Textarea',
            self::FIELD_ARRAY => 'Array',
            self::FIELD_WYSIWYG => 'Rich editor (WYSIWYG)',
            self::FIELD_NUMBER => 'Number',
            self::FIELD_EMAIL => 'E-mail',
            self::FIELD_URL => 'URL'
        ],
        'Date and time' => [
            self::FIELD_DATE => 'Date',
            self::FIELD_TIME => 'Time',
            self::FIELD_DATETIME => 'Date and time',
            self::FIELD_WEEK => 'Week',
            self::FIELD_MONTH => 'Month'
        ],
        'Lists' => [
            self::FIELD_SELECT => 'Select',
            self::FIELD_RADIO => 'Radios',
            self::FIELD_DATALIST => 'Data list'
        ],
        'Other' => [
            self::FIELD_CHECKBOX => 'Checkbox',
            self::FIELD_COLOR => 'Color'
        ],
        'Files' => [
            self::FIELD_FILE => 'File',
            self::FIELD_IMAGE => 'Image',
            self::FIELD_PDF => 'PDF document',
            self::FIELD_ARCHIVE => 'Archive',
            self::FIELD_AUDIO => 'Audio',
            self::FIELD_VIDEO => 'Video',
            self::FIELD_GRAPHIC => 'Graphic design files'
        ]
    ];

    /**
     * Accept map for file input
     * 
     * @var array
     */
    protected static $accept = [
        self::FIELD_IMAGE => 'image/apng, image/avif, image/gif, image/jpeg, image/png, image/svg+xml, image/webp',
        self::FIELD_PDF => '.pdf',
        self::FIELD_ARCHIVE => '.zip,.rar,.7z,.tar,.gz,.bz2,.xz,.tgz,.tbz2,.txz,.zst,.lz,.lzma,.lzo,.arj,.cab,.ace,.arc,.pak,.dmg,.cpio,.z,.sit,.sitx,.hqx,.lzh,.xxe,.rpm,.deb,.pea,.wim,.sqsh',
        self::FIELD_AUDIO => '.mp3,.wav,.flac,.aac,.ogg,.wma,.m4a,.opus,.aiff,.aif,.alac,.dsd,.dsf,.dff,.ac3,.dts,.mp2,.amr,.caf,.mid,.midi,.vqf,.ra,.rm,.ram,.gsm',
        self::FIELD_VIDEO => '.mp4,.mkv,.mov,.avi,.wmv,.flv,.webm,.m4v,.3gp,.3g2,.ogv,.rm,.rmvb,.ts,.mts,.m2ts,.vob,.mxf,.f4v,.divx,.xvid,.asf,.mpg,.mpeg,.dv,.yuv,.vp9',
        self::FIELD_GRAPHIC => '.ai,.cdr,.eps,.svg,.svgz,.wmf,.emf,.fxg,.odg,.xar,.psd,.psb,.xcf,.pdn,.afphoto,.clip,.kra,.indt,.indd,.idml,.qxd,.qxp'
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
            self::FIELD_ARRAY,
            self::FIELD_WYSIWYG,
            self::FIELD_NUMBER,
            self::FIELD_EMAIL,
            self::FIELD_URL
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
            self::FIELD_DATETIME,
            self::FIELD_WEEK,
            self::FIELD_MONTH
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
            self::FIELD_CHECKBOX,
            self::FIELD_COLOR
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
        return in_array($type, self::getFileTypes());
    }

    /**
     * Fetch file type constants
     * 
     * @return array
     */
    public static function getFileTypes()
    {
        return [
            self::FIELD_FILE,
            self::FIELD_IMAGE,
            self::FIELD_PDF,
            self::FIELD_ARCHIVE,
            self::FIELD_AUDIO,
            self::FIELD_VIDEO,
            self::FIELD_GRAPHIC
        ];
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
