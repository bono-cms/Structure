<?php

namespace Structure\Storage\MySQL;

use Krystal\Db\Sql\RawSqlFragment;
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
     * @param boolean $sort Whether to sort by order. If true, sorted by order, otherwise by last id
     * @return array
     */
    public function fetchAll($sort)
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
                            FieldMapper::column('collection_id') => self::getRawColumn('id')
                       ])
                       ->groupBy($columns);

        if ($sort == true) {
            $db->orderBy(new RawSqlFragment(
                    sprintf('%s, CASE WHEN %s = 0 THEN %s END DESC', self::column('order'), self::column('order'), self::column('id'))
                ));
        } else {
            $db->orderBy(self::column('id'))
               ->desc();
        }

        return $db->queryAll();
    }
}
