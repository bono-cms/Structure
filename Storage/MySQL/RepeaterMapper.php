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
     * Insert values only
     * 
     * @param int $repeaterId
     * @param array $values (Field ID => Text value pairs)
     * @return boolean Depending on success
     */
    private function insertValues($repeaterId, array $values)
    {
        // Columns to be inserted
        $columns = [
            'repeater_id',
            'field_id',
            'value'
        ];

        $rows = [];
        
        foreach ($values as $fieldId => $value) {
            $rows[] = [
                'repeater_id' => $repeaterId,
                'field_id' => $fieldId,
                'value' => $value
            ];
        }

        return $this->db->insertMany(RepeaterValueMapper::getTableName(), $columns, $values)
                        ->execute();
    }

    /**
     * Perform batch INSERT
     * 
     * @param array $input
     * @return array
     */
    public function batchInsert(array $input)
    {
        $data = [
            'collection_id' => $input['repeater']['collection_id'],
            'order' => 0, // @TODO: From form
            'hidden' => 0 // @TODO: From form
        ];

        // Insert new row and get its id
        $row = $this->persistRow($data, array_keys($data));
        $id = $row[$this->getPk()];

        $columns = [
            'collection_id',
            'order',
            'hidden'
        ];

        // Values to be inserted. We'll grab them from current input
        $rows = [];

        // 1. Prepare values for BATCH insert query
        foreach ($input['record'] as $fieldId => $value) {
            // Append in exactly the same order as in $columns
            $rows[] = [
                $id,
                $fieldId,
                $value
            ];
        }

        return $this->insertValues($id, $rows);
    }

    /**
     * Fetch all by collection id
     * 
     * @param int $id Collection id
     * @return array
     */
    public function fetchAllByCollectionId($collectionId)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->whereEquals('collection_id', $collectionId)
                       ->orderBy('order');

        return $db->queryAll();
    }
}
