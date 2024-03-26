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

        return [
            'id' => $id,
            'rows' => $rows
        ];
    }

    /**
     * Fetch row data by repeater id
     * 
     * @param int $repeaterId
     * @return array
     */
    public function fetchByRepeaterId($repeaterId)
    {
        // Columns be selected
        $columns = [
            FieldMapper::column('id') => 'field_id',
            RepeaterValueMapper::column('id'),
            RepeaterValueMapper::column('repeater_id'),
            RepeaterValueMapper::column('value')
        ];

        $db = $this->db->select($columns)
                       ->from(FieldMapper::getTableName())
                       // Repeater relation
                       ->leftJoin(RepeaterValueMapper::getTableName(), [
                            RepeaterValueMapper::column('field_id') => FieldMapper::getRawColumn('id'),
                            RepeaterValueMapper::column('repeater_id') => $repeaterId
                       ])
                       ->orderBy(RepeaterValueMapper::column('id'))
                       ->desc();

        return $db->queryAll();
    }
}
