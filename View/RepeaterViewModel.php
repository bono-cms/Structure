<?php

namespace Structure\View;

use Structure\Collection\FieldTypeCollection;

final class RepeaterViewModel
{
    /**
     * Generate name for delete input
     * 
     * @return string
     */
    public static function createDeleteNs()
    {
        return 'delete[record][]';
    }

    /**
     * Generate name for delete translatable input
     * 
     * @return string
     */
    public static function createDeleteTranslateNs($langId)
    {
        return sprintf('delete[translation][%s]', $langId);
    }

    /**
     * Creates a group name for translation input
     * 
     * @param string $key Group key
     * @param string $id Entity id
     * @param string $languageId
     * @return string
     */
    public static function createTranslatenNs($key, $id, $languageId)
    {
        return sprintf('translation[%s][%s][%s]', $id, $languageId, $key);
    }

    /**
     * Create columns for the widget
     * 
     * @param array $rows
     * @param array
     */
    public static function createColumns(array $rows)
    {
        if (empty($rows)) {
            return $rows;
        }

        $count = count($rows);

        // Ignored columns
        $ignored = [
            'repeater_id'
        ];

        $rows = array_values($rows)[$count - 1]; // Grab by last index
        $output = [];

        $fieldTypeCollection = new FieldTypeCollection;

        foreach ($rows as $column => $value) {
            $data = [
                'column' => $column
            ];

            // Exception for ID column
            if ($column == 'id') {
                $data['label'] = '#';
            }

            // Exception for Type column
            if ($column == 'type') {
                // Grab name from collection
                $data['value'] = function($row) use ($fieldTypeCollection){
                    return $fieldTypeCollection->findByKey($row['type']);
                };
            }

            // Do we encounter ignored columns?
            if (in_array($column, $ignored)) {
                continue;
            }

            $output[] = $data;
        }

        return $output;
    }
}
