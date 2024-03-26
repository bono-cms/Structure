<?php

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
