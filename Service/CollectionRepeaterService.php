<?php

namespace Structure\Service;

use Structure\Storage\CollectionRepeaterMapperInterface;

final class CollectionRepeaterService
{
    /**
     * Field mapper interface
     * 
     * @var \Structure\Storage\CollectionRepeaterMapperInterface
     */
    private $collectionRepeaterMapper;

    /**
     * State initialization
     * 
     * @param \Structure\Storage\CollectionRepeaterMapperInterface $collectionRepeaterMapper
     * @return void
     */
    public function __construct(CollectionFieldMapperInterface $collectionRepeaterMapper)
    {
        $this->collectionRepeaterMapper = $collectionRepeaterMapper;
    }
}
