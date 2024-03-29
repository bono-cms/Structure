<?php

namespace Structure\Storage\MySQL;

use Krystal\Db\Sql\RawSqlFragment;
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
     * Fetch ids by type constant
     * 
     * @param string $column
     * @param string $value
     * @param array $types Array of constants
     * @return array
     */
    private function fetchByType($column, $value, array $types)
    {
        $db = $this->db->select(self::column('repeater_id'))
                       ->from(self::getTableName())
                       // Repeater relation
                       ->innerJoin(FieldMapper::getTableName(), [
                            FieldMapper::column('id') => self::getRawColumn('field_id')
                       ])
                       ->whereEquals($column, $value)
                       ->andWhereNotEquals(self::column('value'), '')
                       ->andWhereIn(FieldMapper::column('type'), $types);

        return $db->queryAll('repeater_id');
    }

    /**
     * Fetch by collection id
     * 
     * @param int $collectionId
     * @param array $types
     * @return array
     */
    public function fetchByCollectionId($collectionId, array $types)
    {
        return $this->fetchByType(FieldMapper::column('collection_id'), $collectionId, $types);
    }

    /**
     * Fetch by field id
     * 
     * @param int $fieldId
     * @param array $types
     * @return array
     */
    public function fetchByFieldId($fieldId, array $types)
    {
        return $this->fetchByType(self::column('field_id'), $fieldId, $types);
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
     * @param boolean $sort Whether to sort by order. If true, sorted by order, otherwise by last id
     * @param boolean $published Whether to fetch only published ones
     * @return array
     */
    public function fetchAll($collectionId, $sort, $published)
    {
        $db = $this->createSharedQuery()
                   ->whereEquals(RepeaterMapper::column('collection_id'), $collectionId);

        if ($published == true) {
            $db->andWhereEquals(RepeaterMapper::column('published'), '1');
        }

        if ($sort == true) {
            $db->orderBy(new RawSqlFragment(
                    sprintf('%s, CASE WHEN %s = 0 THEN %s END DESC', RepeaterMapper::column('order'), RepeaterMapper::column('order'), self::column('id'))
                ));
        } else {
            $db->orderBy(self::column('id'))
               ->desc();
        }

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
