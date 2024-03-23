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
        $columns = [
            'collection_id',
            'field_id',
            'order',
            'value',
            'hidden'
        ];

        // Values to be inserted. We'll grab them from current input
        $values = [];

        // 1. Prepare values for BATCH insert query
        foreach ($input['record'] as $fieldId => $value) {
            // Append in exactly the same order as in $columns
            $values[] = [
                $input['repeater']['collection_id'],
                $fieldId,
                0, // @TODO: Grab from form
                $value,
                0 // @TODO: Grab from form
            ];
        }

        // 2. Done with preparing data. Now simply run query to insert it
        return $this->db->insertMany(self::getTableName(), $columns, $values)->execute();
    }
}
