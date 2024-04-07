<?php

namespace Structure\Storage;

interface FieldMapperInterface
{
    /**
     * Fetch all fields by collection id
     * 
     * @param int $collectionId
     * @param boolean $sort Whether to perform sorting by order
     * @return array
     */
    public function fetchByCollectionId($collectionId, $sort);

    /**
     * Checks whether name is already taken
     * 
     * @param string $target
     * @return boolean
     */
    public function nameExists($target);

    /**
     * Checks whether alias is already taken
     * 
     * @param string $target
     * @return boolean
     */
    public function aliasExists($target);
}
