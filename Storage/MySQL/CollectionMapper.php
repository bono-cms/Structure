<?php

namespace Structure\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;

final class CollectionMapper extends AbstractMapper
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_structure_collections');
    }
}
