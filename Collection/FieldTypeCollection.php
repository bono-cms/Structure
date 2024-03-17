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
    /* Shared type constants */
    const FIELD_TEXT = 1;
    const FIELD_TEXTAREA = 2;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        self::FIELD_TEXT => 'Text',
        self::FIELD_TEXTAREA => 'Textarea'
    ];
}
