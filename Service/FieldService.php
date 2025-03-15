<?php

namespace Structure\Service;

use Structure\Storage\FieldMapperInterface;
use Krystal\Stdlib\ArrayUtils;

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
     * @param boolean $sort Whether to perform sorting by order
     * @return array
     */
    public function fetchByCollectionId($collectionId, $sort)
    {
        return $this->fieldMapper->fetchByCollectionId($collectionId, $sort);
    }

    /**
     * Fetch fields (<Field ID> => <Field Name>) by collection id
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchFields($collectionId)
    {
        $rows = $this->fetchByCollectionId($collectionId, true);

        // If found non-empty
        if ($rows) {
            return ArrayUtils::arrayList($rows, 'id', 'name');
        }

        return [];
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
     * Checks whether name is already taken
     * 
     * @param int $collectionId
     * @param string $name
     * @return boolean
     */
    public function nameExists($collectionId, $name)
    {
        return $this->fieldMapper->nameExists($collectionId, $name);
    }

    /**
     * Checks whether alias is already taken
     * 
     * @param int $collectionId
     * @param string $alias
     * @return boolean
     */
    public function aliasExists($collectionId, $alias)
    {
        return $this->fieldMapper->aliasExists($collectionId, $alias);
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
