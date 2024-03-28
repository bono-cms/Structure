<?php

namespace Structure\Storage;

interface RepeaterValueMapperInterface
{
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
     * @parma int $fieldId
     * @param int $repeaterId
     * @return array
     */
    public function fetchPrimaryKeys($fieldId, $repeaterId);

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
     * @return array
     */
    public function fetchAll($collectionId);
}
