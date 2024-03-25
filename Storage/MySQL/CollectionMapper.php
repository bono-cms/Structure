<?php

namespace Structure\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;
use Structure\Storage\CollectionMapperInterface;

final class CollectionMapper extends AbstractMapper implements CollectionMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_structure_collections');
    }

    /**
     * Fetch all collections with field count on them
     * 
     * @return array
     */
    public function fetchAll()
    {
        // Columns to be selected
        $columns = [
            self::column($this->getPk()),
            self::column('name'),
            self::column('order')
        ];

        $db = $this->db->select($columns)
                       ->count(FieldMapper::column('id'), 'count') // Count attached fields
                       ->from(self::getTableName())
                       // Field relation
                       ->leftJoin(FieldMapper::getTableName(), [
                            FieldMapper::column('collection_id') => self::getRawColumn($this->getPk())
                       ])
                       ->groupBy($columns)
                       ->orderBy(self::column($this->getPk()));

        return $db->queryAll();
    }
}
