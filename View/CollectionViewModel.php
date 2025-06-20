<?php

namespace Structure\View;

use Structure\Collection\LayoutCollection;
use Structure\Collection\FieldTypeCollection;

final class CollectionViewModel
{
    /**
     * Determines whether a given count exceeds a specified limit, treating 0 as "unlimited."
     * 
     * @param int $limit
     * @param int $count
     * @return boolean
     */
    public static function limitExceeds($limit, $count)
    {
        $limit = intval($limit);
        $count = intval($count);

        return $limit > 0 && ($count > $limit);
    }

    /**
     * Get column classes by layout contant
     * 
     * @param int $layout
     * @param array $fields
     * @return array
     */
    public static function getColumns($layout, array $fields)
    {
        if ($layout == LayoutCollection::LAYOUT_LEFT_GRID_RIGHT_FORM) {
            return [
                'first' => 'col-lg-7',
                'second' => 'col-lg-5'
            ];
        }

        if ($layout == LayoutCollection::LAYOUT_TOP_GRID_BOTTOM_FORM) {
            return [
                'first' => 'col-lg-12',
                'second' => 'col-lg-12'
            ];
        }

        if ($layout == LayoutCollection::LAYOUT_AUTO) {
            $stop = 5; // Column count to stop

            // Is there any WYSIWYG editors?
            foreach ($fields as $index => $field) {
                if ($field['type'] == FieldTypeCollection::FIELD_WYSIWYG || $index + 1 == $stop) {
                    return [
                        'first' => 'col-lg-12',
                        'second' => 'col-lg-12'
                    ];
                }
            }

            return [
                'first' => 'col-lg-7',
                'second' => 'col-lg-5'
            ];
        }
    }
}
