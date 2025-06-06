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
     * Fetches sorting method constant by collection id
     * 
     * @param int $id Collection id
     * @return string
     */
    public function fetchSortingMethod($id)
    {
        return $this->collectionMapper->fetchSortingMethod($id);
    }

    /**
     * Fetch all collections
     * 
     * @param boolean $sort Whether to sort by order. If true, sorted by order, otherwise by last id
     * @return array
     */
    public function fetchAll($sort)
    {
        return $this->collectionMapper->fetchAll($sort);
    }
}
