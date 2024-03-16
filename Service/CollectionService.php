<?php

namespace Structure\Service;

use Structure\Storage\CollectionMapperInterface;

final class CollectionService
{
    /**
     * Compliant collection mapper
     * 
     * @var \Structure\Storage\CollectionMapperInterface
     */
    private $collectionMapper;

    /**
     * State initialization
     * 
     * @param \Structure\Storage\CollectionMapperInterface $collectionMapper
     * @return void
     */
    public function __construct(CollectionMapperInterface $collectionMapper)
    {
        $this->collectionMapper = $collectionMapper;
    }
}
