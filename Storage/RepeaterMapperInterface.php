<?php

namespace Structure\Storage;

interface RepeaterMapperInterface
{
    /**
     * Fetch all by collection id
     * 
     * @param int $id Collection id
     * @return array
     */
    public function fetchAllByCollectionId($collectionId);
}
