<?php

namespace Structure\Storage\MySQL;

use Krystal\Db\Sql\RawSqlFragment;
use Cms\Storage\MySQL\AbstractMapper;
use Structure\Storage\FieldMapperInterface;

final class FieldMapper extends AbstractMapper implements FieldMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_structure_collections_fields');
    }

    /**
     * Fetch all fields by collection id
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchByCollectionId($collectionId)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->whereEquals('collection_id', $collectionId)
                       ->orderBy(new RawSqlFragment(
                            '`order`, CASE WHEN `order` = 0 THEN `id` END DESC'
                       ));

        return $db->queryAll();
    }
}
