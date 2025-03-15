<?php

namespace Structure\Collection;

use Krystal\Stdlib\ArrayCollection;

final class SortingCollection extends ArrayCollection
{
    /* Layout constants */
    const SORTING_BY_ID = 1;
    const SORTING_BY_ORDER = 2;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        self::SORTING_BY_ID => 'Sort by latest items',
        self::SORTING_BY_ORDER => 'Sort by sorting number'
    ];
}