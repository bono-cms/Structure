<?php

namespace Structure\View;

use Krystal\Form\Element;
use Structure\Collection\FieldTypeCollection;

final class RepeaterViewModel
{
    /**
     * Creates unique signature for input element
     * 
     * @return string
     */
    public static function createSignature()
    {
        return sprintf('uniq-%s', uniqid());
    }

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
                'label' => $field['name'],
                'hidden' => !$field['gridable']
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

            // Is there a checkbox?
            if ($field['type'] == FieldTypeCollection::FIELD_CHECKBOX) {
                $column['translateable'] = true;
                $column['value'] = function($row) use ($field) {
                    $value = $row[$field['alias']];
                    return $value == 1 ? 'Yes' : 'No';
                };
            }

            // Is this a number field?
            if ($field['type'] == FieldTypeCollection::FIELD_NUMBER) {
                $column['value'] = function($row) use ($field) {
                    $value = $row[$field['alias']];
                    return number_format($value);
                };
            }

            // Is this a URL field?
            if ($field['type'] == FieldTypeCollection::FIELD_URL) {
                $column['value'] = function($row) use ($field) {
                    $value = $row[$field['alias']];
                    return Element::link($value, $value, ['target' => '_blank']);
                };
            }

            $output[] = $column;
        }

        return $output;
    }
}
