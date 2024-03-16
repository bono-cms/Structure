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
     * Fetch all collections
     * 
     * @return array
     */
    public function fetchAll()
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->orderBy($this->getPk());

        return $db->queryAll();
    }
}
