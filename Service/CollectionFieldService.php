<?php

namespace Structure\Service;

use Structure\Storage\CollectionFieldMapperInterface;

final class CollectionFieldService
{
    /**
     * Comploiant field mapper
     * 
     * @var \Structure\Storage\CollectionFieldMapperInterface
     */
    private $collectionFieldMapper;

    /**
     * State initialization
     * 
     * @param \Structure\Storage\CollectionFieldMapperInterface $collectionFieldMapper
     * @return void
     */
    public function __construct(CollectionFieldMapperInterface $collectionFieldMapper)
    {
        $this->collectionFieldMapper = $collectionFieldMapper;
    }
}
