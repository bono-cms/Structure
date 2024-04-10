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
     * @param boolean $sort Whether to perform sorting by order
     * @return array
     */
    public function fetchByCollectionId($collectionId, $sort)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->whereEquals('collection_id', $collectionId);

        if ($sort == true) {
            $db->orderBy(new RawSqlFragment(
                '`order`, CASE WHEN `order` = 0 THEN `id` END DESC'
            ));
        } else {
            $db->orderBy('id')
               ->desc();
        }

        return $db->queryAll();
    }

    /**
     * Checks whether name is already taken
     * 
     * @param int $collectionId
     * @param string $name
     * @return boolean
     */
    public function nameExists($collectionId, $name)
    {
        return $this->valuesExist([
            'collection_id' => $collectionId,
            'name' => $name
        ]);
    }

    /**
     * Checks whether alias is already taken
     * 
     * @param int $collectionId
     * @param string $alias
     * @return boolean
     */
    public function aliasExists($collectionId, $alias)
    {
        return $this->valuesExist([
            'collection_id' => $collectionId,
            'alias' => $alias
        ]);
    }
}
