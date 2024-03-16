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

    /**
     * Save input data
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->collectionMapper->persist($input);
    }

    /**
     * Deletes a collection by its id
     * 
     * @param int $id
     * @return boolean
     */
    public function deleteByPk($id)
    {
        return $this->collectionMapper->deleteByPk($id);
    }

    /**
     * Returns last id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->collectionMapper->getMaxId();
    }

    /**
     * Finds collection by its id
     * 
     * @param int $id collection id
     * @return mixed
     */
    public function fetchById($id)
    {
        return $this->collectionMapper->findByPk($id);
    }

    /**
     * Fetch all collections
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->collectionMapper->fetchAll();
    }
}
