<?php

namespace Structure\Storage;

interface RepeaterMapperInterface
{
    /**
     * Fetch repeater with its all values by id
     * 
     * @param int $repeaterId
     * @return array
     */
    public function fetchById($repeaterId);

    /**
     * Fetch all records with ther values by collection id
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchAll($collectionId);
}
