<?php

namespace Structure\Service;

use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;
use Krystal\Text\TextUtils;
use Structure\Collection\FieldTypeCollection;
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
     * File input service
     * 
     * @var \Structure\Service\FileInput
     */
    private $fileInput;

    /**
     * State initialization
     * 
     * @param \Structure\Storage\RepeaterMapperInterface $repeaterMapper
     * @param \Structure\Storage\RepeaterValueMapperInterface $repeaterValueMapper
     * @param \Structure\Service\FileInput $fileInput
     * @return void
     */
    public function __construct(RepeaterMapperInterface $repeaterMapper, RepeaterValueMapperInterface $repeaterValueMapper, FileInput $fileInput)
    {
        $this->repeaterMapper = $repeaterMapper;
        $this->repeaterValueMapper = $repeaterValueMapper;
        $this->fileInput = $fileInput;
    }

    /**
     * Count repeaters by collection id (required for pagination)
     * 
     * @param int $collectionId
     * @return int
     */
    public function countRepeaters($collectionId)
    {
        return $this->repeaterValueMapper->countRepeaters($collectionId);
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
     * Delete files by field id
     * 
     * @param int $id Field id
     * @return boolean
     */
    public function deleteFilesByFieldId($id)
    {
        $rows = $this->repeaterValueMapper->fetchByFieldId($id, [
            FieldTypeCollection::FIELD_FILE
        ]);

        return $this->fileInput->purgeDir($rows);
    }

    /**
     * Delete files by collection id
     * 
     * @param int $id Collection id
     * @return boolean
     */
    public function deleteFilesByCollectionId($id)
    {
        $rows = $this->repeaterValueMapper->fetchByCollectionId($id, [
            FieldTypeCollection::FIELD_FILE
        ]);

        return $this->fileInput->purgeDir($rows);
    }

    /**
     * Delete files by repeater id
     * 
     * @param int $id Repeater id
     * @return boolean
     */
    public function deleteFilesByRepeaterId($id)
    {
        return $this->fileInput->purgeDir($id);
    }

    /**
     * Deletes a record with its all relations
     * 
     * @param int $id Repeater id
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

        foreach ($fields as &$field) {
            foreach ($rows as $row) {
                // Catch current field
                if ($field['id'] == $row['field_id']) {
                    $field['value'] = $row['value'];

                    // Append translations, if required
                    if (isset($field['translatable']) && $field['translatable'] == 1) {
                        $field['translations'] = ArrayUtils::arrayList($this->repeaterValueMapper->fetchTranslations($row['id']), 'lang_id', 'value');
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
     * Returns pagination instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->repeaterValueMapper->getPaginator();
    }

    /**
     * Fetch paginated resutls
     * 
     * @param int $collectionId
     * @param int $langId If provided, all translatable fields will return values with current language
     * @param array $sortingOptions Sorting options
     * @param boolean $published Whether to filter only by published ones
     * @param int $page Current page number
     * @param int $itemsPerPage Items per page to be returned
     * @return array
     */
    public function fetchPaginated($collectionId, $langId, array $sortingOptions = [], $published = false, $page = null, $itemsPerPage = null)
    {
        $rows = $this->repeaterValueMapper->fetchPaginated($collectionId, $sortingOptions, $published, $page, $itemsPerPage);
        $translations = $this->repeaterValueMapper->fetchAllTranslations($collectionId, $langId);

        // Append translations, if available
        if ($translations) {
            foreach ($rows as &$row) {
                foreach ($translations as &$translation) {
                    if ($row['repeater_id'] == $translation['repeater_id']) {
                        $row[$translation['alias']] = $translation['value'];
                    }
                }
            }
        }

        return $rows;
    }

    /**
     * Fetch all by collection id
     * 
     * @param int $collectionId
     * @param int $langId If provided, all translatable fields will return values with current language
     * @param boolean $reset Whether to return an indexed array
     * @param boolean $sort Whether to sort by order. If true, sorted by order, otherwise by last id
     * @param boolean $published Whether to fetch only published ones
     * @return array
     */
    public function fetchAll($collectionId, $langId = null, $reset = false, $sortingMethod = false, $published = false)
    {
        $rows = $this->repeaterValueMapper->fetchAll($collectionId, $sortingMethod, $published);
        $output = [];

        // Turn rows into one single row
        foreach ($rows as $row) {
            $key = $row['repeater_id'];

            // Static keys are added here
            if (!isset($output[$key])) {
                $output[$key] = [
                    'id' => $row['id'] // Value ID
                ];

                if ($reset === false) {
                    $output[$key]['repeater_id'] = $row['repeater_id']; // Primary parent ID
                }
            }

            // If we have translatable field
            if ($langId !== null && $row['translatable'] == 1) {
                /**
                 * @TODO: This can be optimized, if we fetch all translations at once outside of this iteration
                 * And then here we compare against available translation data.
                 * So that we'll avoid quering a database each time a match occurs.
                 */
                $row['value'] = $this->repeaterValueMapper->fetchTranslations($row['id'], $langId);
            }

            // Convert to array line by line
            if ($row['type'] == FieldTypeCollection::FIELD_ARRAY) {
                $row['value'] = explode("\n", $row['value']);
            }

            // Dynamic keys are added here
            $output[$key] = array_merge($output[$key], [
                $row['alias'] => $row['value']
            ]);
        }

        // Do we need to reset array indexes?
        if ($reset === true) {
            $output = array_values($output);
        }

        return $output;
    }

    /**
     * Filter records
     * 
     * @param array $rows
     * @param array $filter
     * @return array
     */
    public function filterRecords(array $rows, array $filter = [])
    {
        $output = [];

        foreach ($filter as $alias => $value) {
            // Do not process empty values
            if (!empty($value)) {
                foreach ($rows as $row) {
                    if (isset($row[$alias]) && TextUtils::contains($row[$alias], $value)) {
                        $output[] = $row;
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Update values
     * 
     * @param int $repeaterId
     * @param array $input New data to be updated
     * @param array $files Optional files
     * @return boolean
     */
    public function update($repeaterId, array $input, array $files)
    {
        // 1. Update repeater
        $this->repeaterMapper->persist($input['repeater']);

        // 2. Update values
        $rows = $this->repeaterMapper->fetchByRepeaterId($repeaterId);

        // Update input with files, if available
        $input = $this->processUploads($repeaterId, $input, $files, true);

        // Override value with new coming values
        foreach ($rows as &$row) {
            if (isset($input['record'][$row['field_id']])) {
                $row['value'] = $input['record'][$row['field_id']];
            }

            // Update translations
            if ($row['translatable'] == 1) {
                $ids = $this->repeaterValueMapper->fetchPrimaryKeys($row['repeater_id'], $row['field_id']);
                /**
                 * Scenario: When a new translatable field has been added, but this repeater has not yet a relation with that field.
                 * In this case, no value exists and it will result with disability to update a value.
                 * To workaround this, we'll create an empty row and get its last id and then append to a stack
                 */
                if (!$ids) {
                    $this->repeaterValueMapper->insertEmpty($repeaterId, $row['field_id']);
                    $ids[] = $this->repeaterValueMapper->getMaxId(); // Created it, now just grab last ID
                }

                // Process with updated now
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
     * @param array $files Optional files
     * @return boolean
     */
    public function save(array $input, array $files)
    {
        // Get repeater ID
        $repeaterId = $this->repeaterMapper->insert($input['repeater']);

        // Update input with files, if available
        $input = $this->processUploads($repeaterId, $input, $files, false);

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
                $this->repeaterValueMapper->saveEntity($entity, []);
            }
        }

        return true;
    }
    
    /**
     * Process file upload inputs
     * 
     * @param int $repeaterId Target repeater field
     * @param array $data Raw input data coming from request
     * @param array $files An array of file instances
     * @return arary Updated data input
     */
    private function processUploads($repeaterId, array $data, array $files, $purge)
    {
        // Do we have explicit file-related fields to delete their values?
        if (isset($data['delete'])) {
            $paths = []; // Paths to be deleted

            // Non-translatable fields
            if (isset($data['delete']['record'])) {
                foreach ($data['delete']['record'] as $fieldId) {
                    $paths[] = $data['record'][$fieldId];
                    $data['record'][$fieldId] = ''; // Erase value
                }
            }

            // Translatable fields
            if (isset($data['delete']['translation'])) {
                foreach($data['delete']['translation'] as $langId => $fieldId) {
                    $paths[] = $data['translation'][$fieldId][$langId]['value'];
                    $data['translation'][$fieldId][$langId]['value'] = ''; // Erase value
                }
            }

            // Purge many paths
            $this->fileInput->purge($paths);
        }

        // Do we have an uploaded file for any translatable field?
        if (isset($files['translation'])) {
            foreach ($files['translation'] as $fieldId => $languages) {
                foreach ($languages as $langId => $file) {
                    $value =& $data['translation'][$fieldId][$langId]['value']; // Attach by reference
                    // Do we need to remove previous file?
                    if ($purge) {
                        $this->fileInput->purge($value);
                    }

                    // Upload a file and override by base its path
                    $value = $this->fileInput->upload($repeaterId, $fieldId, $file['value']);
                }
            }
        }

        // Do we have an uploaded file for any non-translatable field?
        if (isset($files['record'])) {
            foreach ($files['record'] as $fieldId => $file) {
                $value =& $data['record'][$fieldId]; // Attach by reference
                // Do we need to remove previous file?
                if ($purge) {
                    $this->fileInput->purge($value);
                }

                // Upload a file and override by base its path
                $value = $this->fileInput->upload($repeaterId, $fieldId, $file);
            }
        }

        return $data;
    }
}
