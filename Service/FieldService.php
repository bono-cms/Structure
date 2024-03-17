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

    /**
     * Returns last id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->fieldMapper->getMaxId();
    }

    /**
     * Fetch all fields by collection id
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchByCollectionId($collectionId)
    {
        return $this->fieldMapper->fetchByCollectionId($collectionId);
    }

    /**
     * Fetch field by its id
     * 
     * @param int $id
     * @return mixed
     */
    public function fetchById($id)
    {
        return $this->fieldMapper->findByPk($id);
    }

    /**
     * Deletes a field by its id
     * 
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->fieldMapper->deleteByPk($id);
    }

    /**
     * Saves a field
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->fieldMapper->persist($input);
    }
}
