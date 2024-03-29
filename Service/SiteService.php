<?php

namespace Structure\Service;

final class SiteService
{
    /**
     * Repeater service instance
     * 
     * @var \Structure\Service\RepeaterService $repeaterService
     */
    private $repeaterService;

    /**
     * Current language ID
     * 
     * @var int
     */
    private $langId;

    /**
     * State initialization
     * 
     * @param \Structure\Service\RepeaterService $repeaterService
     * @param int $langId Current language ID
     * @return void
     */
    public function __construct(RepeaterService $repeaterService, $langId)
    {
        $this->repeaterService = $repeaterService;
        $this->langId = $langId;
    }

    /**
     * Returns collection data by its id
     * 
     * @param int $id Collection id
     * @param int $langId Language id, in case of override
     * @return array
     */
    public function getCollection($id, $langId = null)
    {
        if (is_null($langId)) {
            $langId = $this->langId;
        }

        return $this->repeaterService->fetchAll($id, $langId, true, true);
    }
}
