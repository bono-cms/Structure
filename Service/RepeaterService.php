<?php

namespace Structure\Service;

use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;
use Structure\Storage\RepeaterMapperInterface;
use Structure\Storage\RepeaterValueMapperInterface;

final class RepeaterService
{
    /**
     * Repeater mapper interface
     * 
     * @var \Structure\Storage\RepeaterMapperInterface
     */
    private $repeaterMapper;

    /**
     * Repeater value mapper interface
     * 
     * @var \Structure\Storage\RepeaterValueMapperInterface
     */
    private $repeaterValueMapper;

    /**
     * State initialization
     * 
     * @param \Structure\Storage\RepeaterMapperInterface $repeaterMapper
     * @param \Structure\Storage\RepeaterValueMapperInterface $repeaterValueMapper
     * @return void
     */
    public function __construct(RepeaterMapperInterface $repeaterMapper, RepeaterValueMapperInterface $repeaterValueMapper)
    {
        $this->repeaterMapper = $repeaterMapper;
        $this->repeaterValueMapper = $repeaterValueMapper;
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
     * @param int $repeaterId
     * @return array
     */
    public function appendValues(array $fields, $repeaterId)
    {
        $rows = $this->repeaterValueMapper->fetchValues($repeaterId);

        // If could not find values, then simply return current $fields
        if (empty($rows)) {
            return $fields;
        }

        // Internal function. Make languages as a hash list.
        $hashLanguages = function(array $rows){
            $output = [];

            foreach ($rows as $row) {
                $output[$row['lang_id']] = $row['value']; 
            }

            return $output;
        };

        foreach ($fields as &$field) {
            foreach ($rows as $row) {
                // Catch current field
                if ($field['id'] == $row['field_id']) {
                    $field['value'] = $row['value'];

                    // Append translations, if required
                    if (isset($field['translatable']) && $field['translatable'] == 1) {
                        $field['translations'] = $hashLanguages($this->repeaterValueMapper->fetchTranslations($row['id']));
                    }
                }
            }
        }

        return $fields;
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
        $rows = $this->repeaterValueMapper->fetchAll($collectionId);
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
        $this->repeaterValueMapper->updateValues($repeaterId, $rows);

        return true;
    }

    /**
     * Saves a repeater
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function save(array $input)
    {
        // Get repeater ID
        $repeaterId = $this->repeaterMapper->insert($input['repeater']);

        // Translatable scenario
        if (isset($input['translatable'])) {
            // IDs of translatable fields
            foreach ($input['translatable'] as $fieldId) {
                // Append empty value
                $input['record'] = $input['record'] + [
                    $fieldId => ''
                ];
            }

            // Oterate over translatable fields only
            foreach ($input['translatable'] as $fieldId) {
                $entity = [
                    'repeater_id' => $repeaterId,
                    'field_id' => $fieldId,
                    'value' => '' // Dummy value
                ];

                // Save entity
                $this->repeaterValueMapper->saveEntity($entity, $input['translation']);
            }
        } else {
            // Non-translatable scenario
            foreach ($input['record'] as $fieldId => $value) {
                $entity = [
                    'repeater_id' => $repeaterId,
                    'field_id' => $fieldId,
                    'value' => $value
                ];

                // Save entity
                $this->repeaterValueMapper->saveEntity($entity);
            }
        }

        return true;
    }
}
