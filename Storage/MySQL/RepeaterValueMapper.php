<?php

namespace Structure\Storage\MySQL;

use InvalidArgumentException;
use Krystal\Cache\MemoryCache;
use Krystal\Db\Sql\QueryBuilder;
use Krystal\Db\Sql\RawSqlFragment;
use Cms\Storage\MySQL\AbstractMapper;
use Structure\Storage\RepeaterValueMapperInterface;
use Structure\Collection\SortingCollection;

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
     * Fetch all translations by collection id filtering by language id
     * 
     * @param int $collectionId
     * @param int $langId Language id filter
     * @return array
     */
    public function fetchAllTranslations($collectionId, $langId)
    {
        $columns = [
            FieldMapper::column('alias'),
            RepeaterValueMapper::column('repeater_id'),
            RepeaterValueTranslationMapper::column('value')
        ];

        $db = $this->db->select($columns)
                       ->from(RepeaterValueTranslationMapper::getTableName())
                       // Repeater relations
                       ->innerJoin(RepeaterValueMapper::getTableName(), [
                        RepeaterValueMapper::column('id') => RepeaterValueTranslationMapper::getRawColumn('id')
                       ])
                       // Field relation
                       ->innerJoin(FieldMapper::getTableName(), [
                        FieldMapper::column('id') => RepeaterValueMapper::getRawColumn('field_id')
                       ])
                       // Constraints
                       ->whereEquals(RepeaterValueTranslationMapper::column('lang_id'), $langId)
                       ->andWhereEquals(FieldMapper::column('collection_id'), $collectionId);

        return $db->queryAll();
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
            FieldMapper::column('translatable'),
            FieldMapper::column('type')
        ];

        $db = $this->db->select($columns, true)
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
     * @param boolean $sortingOptions Sorting options
     * @param boolean $published Whether to fetch only published ones
     * @throws \InvalidArgumentException if invalud $sortingMethod['method'] supplied
     * @return array
     */
    public function fetchAll($collectionId, array $sortingOptions, $published)
    {
        $db = $this->createSharedQuery()
                   ->leftJoin(RepeaterValueTranslationMapper::getTableName(), [
                        RepeaterValueTranslationMapper::column('id') => RepeaterValueMapper::getRawColumn('id')
                   ])
                   ->whereEquals(RepeaterMapper::column('collection_id'), $collectionId);

        if ($published == true) {
            $db->andWhereEquals(RepeaterMapper::column('published'), '1');
        }

        // If no sorting options provided, use default
        if (empty($sortingOptions)) {
            $sortingOptions = [
                'method' => SortingCollection::SORTING_BY_ID
            ];
        }

        switch ($sortingOptions['method']) {
            case SortingCollection::SORTING_BY_ID:
                $db->orderBy(self::column('id'))
                   ->desc();
            break;

            case SortingCollection::SORTING_BY_ORDER:
                $db->orderBy(new RawSqlFragment(
                    sprintf('%s, CASE WHEN %s = 0 THEN %s END DESC', RepeaterMapper::column('order'), RepeaterMapper::column('order'), self::column('id'))
                ));
            break;

            case SortingCollection::SORTING_BY_ALPHABET:
                // Pick sorting column depending if its translatable
                if ($sortingOptions['translatable'] == '1') {
                    $valueColumn = RepeaterValueTranslationMapper::column('value');
                } else {
                    $valueColumn = RepeaterValueMapper::column('value');
                }

                $db->orderBy(new RawSqlFragment(sprintf("%s, CASE WHEN %s = '%s' THEN %s END ASC", $valueColumn, FieldMapper::column('alias'), $sortingOptions['alias'], $valueColumn)));
            break;

            default:
                throw new InvalidArgumentException(sprintf('Unknown sorting type supplied "%s"', $sortingOptions['method']));
        }

        return $db->queryAll();
    }

    /**
     * Count repeaters by collection id (required for pagination)
     * 
     * @param int $collectionId
     * @return int
     */
    public function countRepeaters($collectionId)
    {
        $cache = new MemoryCache();

        if ($cache->has($collectionId)) {
            return $cache->get($collectionId);
        } else {
            $db = $this->db->select()
                           ->count('DISTINCT ' . RepeaterValueMapper::column('repeater_id'), 'count')
                           ->from(RepeaterValueMapper::getTableName())
                           // Collection relation
                           ->innerJoin(FieldMapper::getTableName(), [
                                FieldMapper::column('id') => RepeaterValueMapper::getRawColumn('field_id')
                           ])
                           ->whereEquals(FieldMapper::column('collection_id'), $collectionId);

            $count = (int) $db->queryScalar();
            $cache->set($collectionId, $count, null);
            return $count;
        }
    }

    /**
     * Fetch paginated resutl-set
     * 
     * This method invokes nested queries and aggregate functions, which make it slow
     * Should be only used for larger data-sets
     * 
     * @param int $collectionId
     * @param array $sortingOptions Sorting options
     * @param boolean $published Whether to filter only by published ones
     * @param int $page Current page number
     * @param int $itemsPerPage Items per page to be returned
     * @return array
     */
    public function fetchPaginated($collectionId, array $sortingOptions, $published, $page = null, $itemsPerPage = null)
    {
        $count = $this->countRepeaters($collectionId);

        // Do not process, if no records found
        if ($count === 0) {
            return [];
        }

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
        $aggregateQuery = function($nestedQuery, array $fields = []) use ($sortingOptions){
            $qb = new QueryBuilder();
            $qb->select([
                'repeater_id'
            ]);

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
               ->groupBy(['order', 'repeater_id', 'rn']);

            // If no sorting options provided, use default
            if (empty($sortingOptions)) {
                $sortingOptions = [
                    'method' => SortingCollection::SORTING_BY_ID
                ];
            }

            switch ($sortingOptions['method']) {
                case SortingCollection::SORTING_BY_ID:
                    $qb->orderBy('repeater_id')
                       ->desc();
                break;

                case SortingCollection::SORTING_BY_ORDER:
                    $qb->orderBy('order');
                break;

                case SortingCollection::SORTING_BY_ALPHABET:
                    //@ TODO: Might not work if translatable
                    $qb->orderBy($sortingOptions['alias']);
                break;
            }
            
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
                'fv.repeater_id',
                new RawSqlFragment("CASE WHEN fields.translatable = '1' AND fvt.value IS NOT NULL THEN fvt.value ELSE fv.value END AS value"),
                'repeater.order',
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
            $qb->innerJoin(RepeaterMapper::getTableName() . ' repeater', $constraints)
                ->leftJoin(RepeaterValueTranslationMapper::getTableName() . ' fvt', [
                    'fvt.id' => 'fv.id',
                    'fields.translatable' => "'1'"
                ]
            );

            /* @TODO: Filters by translatable go here */
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

        // Do we only need to limit?
        if ($page === null && $itemsPerPage !== null){
            $db->limit($itemsPerPage);
        }

        // Or we need to apply pagination?
        if ($page !== null && $itemsPerPage !== null) {
            $db->paginateRaw($count, $page, $itemsPerPage);
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
