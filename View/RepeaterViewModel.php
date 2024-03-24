<?php

namespace Structure\View;

final class RepeaterViewModel
{
    /**
     * Create columns for the widget
     * 
     * @param array $rows
     * @param array
     */
    public static function createColumns($rows)
    {
        $rows = array_values($rows)[0];
        $output = [];

        foreach ($rows as $column => $value) {
            $data = [
                'column' => $column
            ];

            // Exception for ID column
            if ($column == 'id') {
                $data['label'] = '#';
            }

            $output[] = $data;
        }

        return $output;
    }
}
