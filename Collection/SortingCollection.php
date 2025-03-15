<?php

namespace Structure\Collection;

use Krystal\Stdlib\ArrayCollection;

final class SortingCollection extends ArrayCollection
{
    /* Layout constants */
    const SORTING_BY_ID = 1;
    const SORTING_BY_ORDER = 2;
    const SORTING_BY_ALPHABET = 3;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        self::SORTING_BY_ID => 'Sort by latest items',
        self::SORTING_BY_ORDER => 'Sort by sorting number',
        self::SORTING_BY_ALPHABET => 'Sort by alphabet'
    ];

    /**
     * Custom sorting - anything that is not sorted by ID or Order
     * 
     * @param int $sortingMethod Sorting constant
     * @return boolean
     */
    public static function isCustomSorting($sortingMethod)
    {
        return !in_array($sortingMethod, [
            self::SORTING_BY_ID,
            self::SORTING_BY_ORDER
        ]);
    }
}
