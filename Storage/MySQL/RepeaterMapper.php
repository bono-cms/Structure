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
     * Creates instance of shared SELECT query
     * 
     * @return \Krysta\Db\Db
     */
    private function createSharedFetchQuery()
    {
        // Columns to be selected
        $columns = [
            RepeaterValueMapper::column('id'),
            RepeaterValueMapper::column('repeater_id'),
            RepeaterValueMapper::column('field_id'),
            RepeaterValueMapper::column('value'),
            FieldMapper::column('name') => 'field',
            FieldMapper::column('alias'),
            FieldMapper::column('translatable')
        ];

        $db = $this->db->select($columns)
                       ->from(RepeaterValueMapper::getTableName())
                       // Repeater relation
                       ->leftJoin(self::getTableName(), [
                            self::column('id') => RepeaterValueMapper::getRawColumn('repeater_id')
                       ])
                       // Field relation
                       ->leftJoin(FieldMapper::getTableName(), [
                            RepeaterValueMapper::column('field_id') => FieldMapper::getRawColumn('id')
                       ]);

        return $db;
    }

    /**
     * Fetch repeater with its all values by id
     * 
     * @param int $repeaterId
     * @return array
     */
    public function fetchById($repeaterId)
    {
        $db = $this->createSharedFetchQuery()
                   ->whereEquals(RepeaterValueMapper::column('repeater_id'), $repeaterId);

        return $db->queryAll();
    }

    /**
     * Fetch all records with ther values by collection id
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchAll($collectionId)
    {
        $db = $this->createSharedFetchQuery()
                   ->whereEquals(self::column('collection_id'), $collectionId)
                   ->orderBy(self::column('order'));

        return $db->queryAll();
    }
}
