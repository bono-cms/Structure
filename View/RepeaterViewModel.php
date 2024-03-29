<?php

namespace Structure\View;

use Krystal\Form\Element;
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
     * @param array $fields Available fields
     * @param array $rows Rows
     * @param array
     */
    public static function createColumns(array $fields, array $rows)
    {
        $output = [];

        foreach ($fields as $field) {
            $column = [
                'column' => $field['alias'],
                'label' => $field['name']
            ];

            // Is this a file type collection?
            if ($field['type'] == FieldTypeCollection::FIELD_FILE) {
                $column['value'] = function($row) use ($field){
                    if (isset($row[$field['alias']])){
                        return Element::link('View file', $row[$field['alias']], ['target' => '_blank']);
                    } else {
                        return null;
                    }
                };
            }

            $output[] = $column;
        }

        return $output;
    }
}
