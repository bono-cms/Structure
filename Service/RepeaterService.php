<?php

namespace Structure\Service;

use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;
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
     * Deletes a record with its all relations
     * 
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->repeaterMapper->deleteByPk($id);
    }

    /**
     * Append values to fields by repeater id
     * 
     * @param array $fields
     * @return array
     */
    public function appendValues(array $fields, $repeaterId)
    {
        $row = $this->fetchValues($repeaterId);

        // If could not find values, then simply return current $fields
        if (empty($row)) {
            return $fields;
        }

        foreach ($fields as &$field) {
            $field['value'] = $row['repeaters'][$field['id']];
        }

        return $fields;
    }

    /**
     * Fetch values by repeater id
     * 
     * @param int $repeaterId
     * @return array
     */
    private function fetchValues($repeaterId)
    {
        $rows = $this->repeaterMapper->fetchValues($repeaterId);

        // If no record found, then immeditelly stop returning empty array
        if (!isset($rows[0])) {
            return [];
        }

        // Collection dynamic fields first
        $repeaters = [];

        foreach ($rows as $row) {
            // This way we do not reset indexes
            $repeaters = $repeaters + [
                $row['field_id'] => $row['value']
            ];
        }

        $row = $rows[0];

        return [
            'translatable' => $row['translatable'],
            'alias' => $row['alias'],
            'field' => $row['field'],
            'repeaters' => $repeaters
        ];
    }

    /**
     * Fetch repeater by its id (single row without any metas)
     * 
     * @param int $id Repeater id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->repeaterMapper->findByPk($id);
    }

    /**
     * Fetch all by collection id
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchAll($collectionId)
    {
        $rows = $this->repeaterMapper->fetchAll($collectionId);
        $output = [];

        // Turn rows into one single row
        foreach ($rows as $row) {
            $key = $row['repeater_id'];

            if (!isset($output[$key])) {
                // Static keys are added here
                $output[$key] = [
                    'id' => $row['id'], // Value ID
                    'repeater_id' => $row['repeater_id'] // Primary parent ID
                ];
            }

            // Dynamic keys are added here
            $output[$key] = array_merge($output[$key], [
                $row['alias'] => $row['value'],
            ]);
        }

        return $output;
    }

    /**
     * Update values
     * 
     * @param int $repeaterId
     * @param array $input New data to be updated
     * @return boolean
     */
    public function update($repeaterId, array $input)
    {
        // 1. Update repeater
        $this->repeaterMapper->persist($input['repeater']);

        // 2. Update values
        $rows = $this->repeaterMapper->fetchByRepeaterId($repeaterId);

        // Override value with new coming values
        foreach ($rows as &$row) {
            $row['value'] = $input['record'][$row['field_id']];
        }

        // Finally run query to update values
        $this->repeaterMapper->updateValues($repeaterId, $rows);

        return true;
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
