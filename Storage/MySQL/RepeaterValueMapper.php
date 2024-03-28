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
     * Fetch translations available translations
     * 
     * @param int $repeaterId
     * @param int $langId Optional language ID filter
     * @return array|string
     */
    public function fetchTranslations($repeaterId, $langId = null)
    {
        // Columns to be selected
        $columns = $langId == null ? [
            'lang_id',
            'value'
        ] : 'value';

        $db = $this->db->select($columns)
                       ->from(RepeaterValueTranslationMapper::getTableName())
                       ->whereEquals('id', $repeaterId);

        if ($langId !== null) {
            $db->andWhereEquals('lang_id', $langId);
            return $db->queryScalar();
        } else {
            return $db->queryAll();
        }
    }

    /**
     * Fetch primary keys by field and repeater ids
     * 
     * @parma int $fieldId
     * @param int $repeaterId
     * @return array
     */
    public function fetchPrimaryKeys($fieldId, $repeaterId)
    {
        $db = $this->db->select(RepeaterValueTranslationMapper::column('id'), true)
                       ->from(RepeaterValueTranslationMapper::getTableName())
                       ->innerJoin(RepeaterValueMapper::getTableName(), [
                            RepeaterValueMapper::column('id') => RepeaterValueTranslationMapper::getRawColumn('id')
                       ])
                       ->whereEquals(RepeaterValueMapper::column('field_id'), $fieldId)
                       ->andWhereEquals(RepeaterValueMapper::column('repeater_id'), $repeaterId);

        return $db->queryAll('id');
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
     * Updates a single translation
     * 
     * @param int $id Row id
     * @param int $langId
     * @param string $value
     * @return boolean
     */
    public function updateValueTranslation($id, $langId, $value)
    {
        $db = $this->db->update(RepeaterValueTranslationMapper::getTableName(), ['value' => $value])
                       ->whereEquals('id', $id)
                       ->andWhereEquals('lang_id', $langId);

        return $db->execute();
    }

    /**
     * Update repeater values by their Ids
     * 
     * @param int $repeaterId
     * @param array $rows
     * @return boolean
     */
    public function updateValues($repeaterId, array $rows)
    {
        foreach ($rows as $row) {
            $db = $this->db->update(RepeaterValueMapper::getTableName(), ['value' => $row['value']])
                           ->whereEquals('field_id', $row['field_id'])
                           ->andWhereEquals('repeater_id', $repeaterId);
            $db->execute();
        }

        return true;
    }
}
