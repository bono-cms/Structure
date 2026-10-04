<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Structure\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;

final class RepeaterValueTranslationMapper extends AbstractMapper
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_structure_fields_values_translations');
    }
}
