<?php

namespace Structure\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;
use Structure\Storage\RepeaterMapperInterface;

final class RepeaterMapper extends AbstractMapper implements RepeaterMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_structure_repeater_fields');
    }

    /**
     * Truncates collection (removes all related records) by its id
     * 
     * @param int $id Collection id
     * @return boolean
     */
    public function truncateByCollectionId($id)
    {
        return $this->deleteByColumn('collection_id', $id);
    }

    /**
     * Inserts a repeater record
     * 
     * @param array $input
     * @return int Last id
     */
    public function insert(array $input)
    {
        $data = [
            'collection_id' => $input['collection_id'],
            'order' => $input['order'],
            'published' => $input['published']
        ];

        // Insert and get last id
        $this->db->insert(self::getTableName(), $data)
                 ->execute();

        return $this->getMaxId();
    }

    /**
     * Fetch row data by repeater id
     * 
     * @param int $repeaterId
     * @return array
     */
    public function fetchByRepeaterId($repeaterId)
    {
        $repeaterId = (int) $repeaterId;

        // Columns be selected
        $columns = [
            FieldMapper::column('id') => 'field_id',
            RepeaterValueMapper::column('id'),
            RepeaterValueMapper::column('repeater_id'),
            RepeaterValueMapper::column('value'),
            FieldMapper::column('translatable')
        ];

        $db = $this->db->select($columns)
                       ->from(FieldMapper::getTableName())
                       // Repeater relation
                       ->leftJoin(RepeaterValueMapper::getTableName(), [
                            RepeaterValueMapper::column('field_id') => FieldMapper::getRawColumn('id'),
                            RepeaterValueMapper::column('repeater_id') => $repeaterId
                       ])
                       // Filter by collection
                       ->innerJoin(RepeaterMapper::getTableName(), [
                            RepeaterMapper::column('collection_id') => FieldMapper::getRawColumn('collection_id'),
                            RepeaterMapper::column('id') => $repeaterId
                       ])
                       ->orderBy(RepeaterValueMapper::column('id'))
                       ->desc();

        return $db->queryAll();
    }
}
