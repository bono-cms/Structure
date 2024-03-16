<?php

namespace Structure\Service;

use Structure\Storage\FieldMapperInterface;

final class FieldService
{
    /**
     * Comploiant field mapper
     * 
     * @var \Structure\Storage\FieldMapperInterface
     */
    private $fieldMapper;

    /**
     * State initialization
     * 
     * @param \Structure\Storage\FieldMapperInterface $collectionFieldMapper
     * @return void
     */
    public function __construct(FieldMapperInterface $fieldMapper)
    {
        $this->fieldMapper = $fieldMapper;
    }
}
