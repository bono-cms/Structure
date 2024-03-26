<?php

namespace Structure\Storage;

interface RepeaterMapperInterface
{
    /**
     * Fetch row data by repeater id
     * 
     * @param int $repeaterId
     * @return array
     */
    public function fetchByRepeaterId($repeaterId);
}
