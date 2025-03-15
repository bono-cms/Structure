<?php

namespace Structure\Storage;

interface RepeaterValueMapperInterface
{
    /**
     * Count repeaters by collection id (required for pagination)
     * 
     * @param int $collectionId
     * @return int
     */
    public function countRepeaters($collectionId);

    /**
     * Inserts empty row
     * 
     * @param int $repeaterId
     * @param int $fieldId
     * @return boolean
     */
    public function insertEmpty($repeaterId, $fieldId);

    /**
     * Update repeater values by their Ids
     * 
     * @param int $repeaterId
     * @param array $values (ID => Value pair).
     *              ID is the primary key of repeater's value.
     *              Value is the new text
     * @return boolean
     */
    public function updateValues($repeaterId, array $rows);

    /**
     * Fetch by collection id
     * 
     * @param int $collectionId
     * @param array $types
     * @return array
     */
    public function fetchByCollectionId($collectionId, array $types);

    /**
     * Fetch by field id
     * 
     * @param int $fieldId
     * @param array $types
     * @return array
     */
    public function fetchByFieldId($fieldId, array $types);

    /**
     * Fetch all translations by collection id filtering by language id
     * 
     * @param int $collectionId
     * @param int $langId Language id filter
     * @return array
     */
    public function fetchAllTranslations($collectionId, $langId);

    /**
     * Fetch translations available translations
     * 
     * @param int $repeaterId
     * @param int $langId Optional language ID filter
     * @return array|string
     */
    public function fetchTranslations($repeaterId, $langId = null);
    
    /**
     * Fetch primary keys by field and repeater ids
     * 
     * @param int $repeaterId
     * @parma int $fieldId
     * @return array
     */
    public function fetchPrimaryKeys($repeaterId, $fieldId);

    /**
     * Fetch repeater's values by id
     * 
     * @param int $repeaterId
     * @return array
     */
    public function fetchValues($repeaterId);

    /**
     * Fetch all records with ther values by collection id
     * 
     * @param int $collectionId
     * @param boolean $sortingOptions Sorting options
     * @param boolean $published Whether to fetch only published ones
     * @throws \InvalidArgumentException if invalud $sortingMethod['method'] supplied
     * @return array
     */
    public function fetchAll($collectionId, array $sortingOptions, $published);

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
    public function fetchPaginated($collectionId, array $sortingOptions, $published, $page = null, $itemsPerPage = null);
}
