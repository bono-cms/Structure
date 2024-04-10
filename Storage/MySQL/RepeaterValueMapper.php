<?php

namespace Structure\Storage\MySQL;

use Krystal\Db\Sql\QueryBuilder;
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
     * @param int $repeaterId
     * @parma int $fieldId
     * @return array
     */
    public function fetchPrimaryKeys($repeaterId, $fieldId)
    {
        $db = $this->db->select('id', true)
                       ->from(self::getTableName())
                       ->whereEquals('field_id', $fieldId)
                       ->andWhereEquals('repeater_id', $repeaterId);

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
     * Count repeaters by collection id (required for pagination)
     * 
     * @param int $collectionId
     * @return int
     */
    private function countRepeaters($collectionId)
    {
        $db = $this->db->select()
                       ->count('DISTINCT ' . RepeaterValueMapper::column('repeater_id'), 'count')
                       ->from(RepeaterValueMapper::getTableName())
                       // Collection relation
                       ->innerJoin(FieldMapper::getTableName(), [
                            FieldMapper::column('id') => RepeaterValueMapper::getRawColumn('field_id')
                       ])
                       ->whereEquals(FieldMapper::column('collection_id'), $collectionId);

        return (int) $db->queryScalar();
    }

    /**
     * Fetch paginated resutls
     * 
     * This method invokes nested queries and aggregate functions, which make it slow
     * Should be only used for larger data-sets
     * 
     * @param int $collectionId
     * @param boolean $published Whether to filter only by published ones
     * @return array
     */
    public function fetchPaginated($collectionId, $published)
    {
        /**
         * Main wrapper query
         * 
         * @param string $nestedQuery Nested query to be appended
         * @param array $filters Alias -> value pair. Only non-translatable ones
         * @return string
         */
        $wrapQuery = function($nestedQuery, array $filters = []){
            $qb = new QueryBuilder();
            $qb->select('*')
               ->from()
               ->openBracket()
               ->append($nestedQuery)
               ->closeBracket()
               ->append('t');
               # Pagination and LIMIT goes here

            // Are filters by alias -> value required?
            if (!empty($filters)) {
                $qb->whereEquals('1', '1');

                foreach ($filters as $alias => $value) {
                    $qb->andWhereEquals($alias, $value);
                }
            }

            return $qb->getQueryString();
        };

        /**
         * Query use to fetch dynamic column names for grouping and ordering a result-set
         * 
         * @param string $nestedQuery Nested query to be appended
         * @param array $fields
         * @return string
         */
        $aggregateQuery = function($nestedQuery, array $fields = []){
            $qb = new QueryBuilder();
            $qb->select('repeater_id');

            // Dynamic values as column names
            foreach ($fields as $field) {
                $qb->max(new RawSqlFragment(sprintf(
                    "case when alias = '%s' then value else null end", $field['alias']
                )))->append($field['alias']);
            }

            $qb->from()
               ->openBracket()
               ->append($nestedQuery)
               ->closeBracket()
               ->append('s')
               ->groupBy(['repeater_id', 'rn'])
               ->orderBy('repeater_id')
               ->desc();

            return $qb->getQueryString();
        };

        /**
         * Main query that actually selects data
         * 
         * @return string
         */
        $selectQuery = function() use ($published, $collectionId){
            // Internal sub-query to aggregate rows
            $countQuery = function(){
                $qb = new QueryBuilder();
                $qb->select()
                   ->count('id')
                   ->from(RepeaterValueMapper::getTableName())
                   ->append(' reference ') # Alias
                   ->where('reference.field_id', '=', 'fv.field_id')
                   ->andWhere('reference.id', '<', 'fv.id');

                return $qb->getQueryString();
            };

            $qb = new QueryBuilder();
            $qb->select([
                'fields.name',
                'fields.order',
                'fv.repeater_id',
                'fv.value',
                'fields.alias',
                sprintf('(%s) rn', $countQuery())
            ])->from(RepeaterValueMapper::getTableName())
              ->append(' fv')
              ->innerJoin(FieldMapper::getTableName() . ' fields', [
                'fields.id' => 'fv.field_id'
              ]);
              
              $constraints = [
                'repeater.id' => 'fv.repeater_id',
                'repeater.collection_id' => $collectionId
              ];

            // Do we need to filter by published only rows?
            if ($published) {
                $constraints['repeater.published'] = 1;
            }

            // Apply constraints
            $qb->innerJoin(RepeaterMapper::getTableName() . ' repeater', $constraints);

            /* Filters by translatable go here */

            return $qb->getQueryString();
        };

        // Select fields by current collection ID
        $fields = $this->db->select(['alias', 'translatable'])
                           ->from(FieldMapper::getTableName())
                           ->whereEquals('collection_id', $collectionId)
                           ->queryAll();

        // Stop immediatelly, if no fields
        if (empty($fields)) {
            return [];
        }

        // Finally, construct a final query
        $query = $wrapQuery(
            $aggregateQuery(
                $selectQuery(), $fields
            )
        );

        // Now run it
        $db = $this->db->raw($query);

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
     * Inserts empty row
     * 
     * @param int $repeaterId
     * @param int $fieldId
     * @return boolean
     */
    public function insertEmpty($repeaterId, $fieldId)
    {
        $db = $this->db->insert(self::getTableName(), [
            'repeater_id' => $repeaterId,
            'field_id' => $fieldId
        ]);

        return $db->execute();
    }

    /**
     * Checks whether row exists by its column values
     * 
     * @param string $table
     * @param array $constraints
     * @return boolean
     */
    private function rowExists($table, array $constraints)
    {
        $db = $this->db->select()
                       ->count('id')
                       ->from($table)
                       ->whereEquals('1', '1');

        foreach ($constraints as $column => $value) {
            $db->andWhereEquals($column, $value);
        }

        return (bool) $db->queryScalar();
    }

    /**
     * Updates a single translation
     * 
     * @param int $id Repeater id
     * @param int $langId
     * @param string $value
     * @return boolean
     */
    public function updateValueTranslation($id, $langId, $value)
    {
        $exists = $this->rowExists(RepeaterValueTranslationMapper::getTableName(), [
            'id' => $id,
            'lang_id' => $langId
        ]);

        if ($exists) {
            // Update one, if exists
            $db = $this->db->update(RepeaterValueTranslationMapper::getTableName(), ['value' => $value])
                           ->whereEquals('id', $id)
                           ->andWhereEquals('lang_id', $langId);
        } else {
            // Otherwise, insert new one
            $db = $this->db->insert(RepeaterValueTranslationMapper::getTableName(), [
                'id' => $id,
                'lang_id' => $langId,
                'value' => $value
            ]);
        }

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
            $exists = $this->rowExists(RepeaterValueMapper::getTableName(), [
                'field_id' => $row['field_id'],
                'repeater_id' => $repeaterId]
            );
            
            if ($exists) {
                $db = $this->db->update(RepeaterValueMapper::getTableName(), ['value' => $row['value']])
                               ->whereEquals('field_id', $row['field_id'])
                               ->andWhereEquals('repeater_id', $repeaterId);
                $db->execute();

            } else {
                $db = $this->db->insert(RepeaterValueMapper::getTableName(), [
                    'field_id' => $row['field_id'],
                    'repeater_id' => $repeaterId,
                    'value' => $row['value'] ? $row['value'] : ''
                ]);

                $db->execute();
            }
        }

        return true;
    }
}
