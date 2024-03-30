<?php

namespace Structure\Collection;

use Krystal\Stdlib\ArrayCollection;

final class LayoutCollection extends ArrayCollection
{
    /* Layout constants */
    const LAYOUT_AUTO = 1;
    const LAYOUT_LEFT_GRID_RIGHT_FORM = 2;
    const LAYOUT_TOP_GRID_BOTTOM_FORM = 3;

    /**
     * {@inheritDoc}
     */
    protected $collection = [
        self::LAYOUT_AUTO => 'Auto layout',
        self::LAYOUT_LEFT_GRID_RIGHT_FORM => 'Left grid / Right form',
        self::LAYOUT_TOP_GRID_BOTTOM_FORM => 'Top grid / Bottom form'
    ];
}
