<?php

namespace Structure\Storage;

interface FieldMapperInterface
{
    /**
     * Fetch all fields by collection id
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchByCollectionId($collectionId);
}
