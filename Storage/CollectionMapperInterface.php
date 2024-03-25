<?php

namespace Structure\Storage;

interface CollectionMapperInterface
{
    /**
     * Fetch all collections with field count on them
     * 
     * @return array
     */
    public function fetchAll();
}
