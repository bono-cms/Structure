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
        $this->setLangId($langId);
    }

    /**
     * Define default language id
     * 
     * @param int $langId
     * @return \Structure\Service\SiteService
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
        return $this;
    }

    /**
     * Returns pagination instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->repeaterService->getPaginator();
    }

    /**
     * Returns paginated collection
     * 
     * @param int $id Collection ID
     * @param int $page Current page
     * @param int $itemsPerPage
     * @return array
     */
    public function getPaginatedCollection($id, $page, $itemsPerPage = 10)
    {
        return $this->repeaterService->fetchPaginated($id, $this->langId, true, true, $page, $itemsPerPage);
    }

    /**
     * Returns collection data by its id
     * 
     * @param int $id Collection id
     * @return array
     */
    public function getCollection($id)
    {
        return $this->repeaterService->fetchAll($id, $this->langId, true, true, true);
    }
}
