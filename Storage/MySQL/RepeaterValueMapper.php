<?php

namespace Structure\Storage\MySQL;

use Structure\Storage\RepeaterValueMapperInterface;
use Cms\Storage\MySQL\AbstractMapper;

final class RepeaterValueMapper extends AbstractMapper implements RepeaterValueMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_structure_repeater_fields_values');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return RepeaterValueTranslationMapper::getTableName();
    }

    /**
     * Creates instance of shared SELECT query
     * 
     * @return \Krysta\Db\Db
     */
    private function createSharedQuery()
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
                       ->leftJoin(RepeaterMapper::getTableName(), [
                            RepeaterMapper::column('id') => RepeaterValueMapper::getRawColumn('repeater_id')
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
        $db = $this->createSharedQuery()
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
        $db = $this->createSharedQuery()
                   ->whereEquals(RepeaterMapper::column('collection_id'), $collectionId)
                   ->orderBy(RepeaterMapper::column('order'));

        return $db->queryAll();
    }

    /**
     * Insert values only
     * 
     * @param int $repeaterId
     * @param array $values (Field ID => Text value pairs)
     * @return boolean Depending on success
     */
    public function insertValues($repeaterId, array $values)
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
     * Update repeater values by their Ids
     * 
     * @param int $repeaterId
     * @param array $values (ID => Value pair).
     *              ID is the primary key of repeater's value.
     *              Value is the new text
     * @return boolean
     */
    public function updateValues($repeaterId, array $rows)
    {
        // Update values by their corresponding ids
        foreach ($rows as $row) {
            $data = [
                'field_id' => $row['field_id'],
                'value' => $row['value'],
                'repeater_id' => $repeaterId
            ];

            // Delete previous, if available
            if (!empty($row['id'])) {
                $this->db->delete()
                         ->from(RepeaterValueMapper::getTableName())
                         ->whereEquals('id', $row['id'])
                         ->execute();

                // Append ID, if provided
                $data['id'] = $row['id'];
            }

            // Insert new
            $this->db->insert(RepeaterValueMapper::getTableName(), $data)
                     ->execute();
        }

        return true;
    }
}
