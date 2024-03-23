<?php

namespace Structure\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;

final class RepeaterValueMapper extends AbstractMapper
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_structure_repeater_fields_values');
    }
}
