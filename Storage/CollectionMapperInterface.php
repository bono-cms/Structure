<?php

namespace Structure\Storage;

interface CollectionMapperInterface
{
    /**
     * Fetch all collections with field count on them
     * 
     * @param boolean $sort Whether to sort by order. If true, sorted by order, otherwise by last id
     * @return array
     */
    public function fetchAll($sort);
}
