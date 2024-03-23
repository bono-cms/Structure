<?php

namespace Structure\Storage;

interface RepeaterMapperInterface
{
    /**
     * Fetch all records with ther values by collection id
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchAll($collectionId);
}
