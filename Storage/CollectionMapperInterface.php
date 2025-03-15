<?php

namespace Structure\Storage;

interface CollectionMapperInterface
{
    /**
     * Fetches sorting method constant by collection id
     * 
     * @param int $id Collection id
     * @return string
     */
    public function fetchSortingMethod($id);

    /**
     * Fetch all collections with field count on them
     * 
     * @param boolean $sort Whether to sort by order. If true, sorted by order, otherwise by last id
     * @return array
     */
    public function fetchAll($sort);
}
