<?php

namespace Structure\Storage\MySQL;

use Cms\Storage\MySQL\AbstractStorageDropper;

final class Dropper extends AbstractStorageDropper
{
    /**
     * {@inheritDoc}
     */
    protected function getTables()
    {
        return [
            CollectionMapper::getTableName(),
            FieldMapper::getTableName(),
            RepeaterMapper::getTableName(),
            RepeaterValueMapper::getTableName(),
            RepeaterValueTranslationMapper::getTableName()
        ];
    }
}
