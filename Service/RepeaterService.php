<?php

namespace Structure\Service;

use Structure\Storage\RepeaterMapperInterface;

final class RepeaterService
{
    /**
     * Field mapper interface
     * 
     * @var \Structure\Storage\RepeaterMapperInterface
     */
    private $repeaterMapper;

    /**
     * State initialization
     * 
     * @param \Structure\Storage\RepeaterMapperInterface $collectionRepeaterMapper
     * @return void
     */
    public function __construct(RepeaterMapperInterface $repeaterMapper)
    {
        $this->repeaterMapper = $repeaterMapper;
    }

    /**
     * Returns last id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->repeaterMapper->getMaxId();
    }

    /**
     * Fetch all by collection id
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchAll($collectionId)
    {
        return $this->repeaterMapper->fetchAll($collectionId);
    }

    /**
     * Saves a repeater
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->repeaterMapper->batchInsert($input);
    }
}
