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
            'order' => $input['repeater']['order'],
            'hidden' => $input['repeater']['hidden']
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
     * Update repeater values by their Ids
     * 
     * @param array $values (ID => Value pair).
     *              ID is the primary key of repeater's value.
     *              Value is the new text
     * @return boolean
     */
    public function updateValues(array $rows)
    {
        // Update values by their corresponding ids
        foreach ($rows as $row) {
            $db = $this->db->update(RepeaterValueMapper::getTableName(), [
                'value' => $row['value']
            ])
            ->whereEquals(RepeaterValueMapper::column('id'), $row['id']);
            $db->execute();
        }

        return true;
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
     * Fetch repeater's values by id
     * 
     * @param int $repeaterId
     * @return array
     */
    public function fetchValues($repeaterId)
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

    /**
     * Fetch row data by repeater id
     * 
     * @param int $repeaterId
     * @return array
     */
    public function fetchByRepeaterId($repeaterId)
    {
        $db = $this->db->select(['id', 'field_id', 'value'])
                       ->from(RepeaterValueMapper::getTableName())
                       ->whereEquals('repeater_id', $repeaterId);

        return $db->queryAll();
    }
}
