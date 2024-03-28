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
     * @param int $langId If provided, all translatable fields will return values with current language
     * @return array
     */
    public function fetchAll($collectionId, $langId = null)
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

            // If we have translatable field
            if ($langId !== null && $row['translatable'] == 1) {
                /**
                 * @TODO: This can be optimized, if we fetch all translations at once outside of this iteration
                 * And then here we compare against available translation data.
                 * So that we'll avoid quering a database each time a mathc occurs.
                 */
                $row['value'] = $this->repeaterValueMapper->fetchTranslations($row['id'], $langId);
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
            if (isset($input['record'][$row['field_id']])) {
                $row['value'] = $input['record'][$row['field_id']];
            }

            // Update translations
            if ($row['translatable'] == 1) {
                $ids = $this->repeaterValueMapper->fetchPrimaryKeys($row['field_id'], $row['repeater_id']);

                foreach ($ids as $id) {
                    foreach ($input['translation'][$row['field_id']] as $langId => $data) {
                        $this->repeaterValueMapper->updateValueTranslation($id, $langId, $data['value']);
                    }
                }
            }
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

        // Translatable scenario (if there's at least one translatable field)
        if (isset($input['translation'])) {
            foreach (array_keys($input['translation']) as $fieldId) {
                $input['record'][$fieldId] = '';  
            }

            // Save records
            foreach ($input['record'] as $fieldId => $value) {
                $entity = [
                    'repeater_id' => $repeaterId,
                    'field_id' => $fieldId,
                    'value' => $value
                ];

                // Get translations for current field, if available
                $translations = isset($input['translation'][$fieldId]) ? $input['translation'][$fieldId] : [];

                // Save entity
                $this->repeaterValueMapper->saveEntity($entity, $translations);
            }

        } else {
            // Non-translatable scenario (when no Translatable fields at all)
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
