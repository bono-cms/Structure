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
     * @param string $hint
     * @param array
     */
    public static function createColumns(array $fields, array $rows, $hint)
    {
        $output = [];

        foreach ($fields as $field) {
            $column = [
                'column' => $field['alias'],
                'label' => $field['name']
            ];

            // Is this a file type collection?
            if (FieldTypeCollection::isFile($field['type'])) {
                $column['value'] = function($row) use ($field, $hint){
                    if (isset($row[$field['alias']])) {
                        $value = $row[$field['alias']];
                        // Image case
                        if ($field['type'] == FieldTypeCollection::FIELD_IMAGE){
                            return Element::image($value, ['class' => 'img-fluid']);
                        }

                        // By default
                        return Element::link($hint, $value, ['target' => '_blank']);
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
