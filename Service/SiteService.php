<?php

namespace Structure\Service;

use Krystal\Http\RequestInterface;

final class SiteService
{
    /**
     * Repeater service instance
     * 
     * @var \Structure\Service\RepeaterService $repeaterService
     */
    private $repeaterService;

    /**
     * Collection service instance
     * 
     * @var \Structure\Service\CollectionService $collectionService
     */
    private $collectionService;

    /**
     * Request instance
     * 
     * @var \Krystal\Http\RequestInterface
     */
    private $request;

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
     * @param \Structure\Service\CollectionService $collectionService
     * @param \Krystal\Http\RequestInterface $request
     * @param int $langId Current language ID
     * @return void
     */
    public function __construct(
        RepeaterService $repeaterService, 
        CollectionService $collectionService, 
        RequestInterface $request, 
        $langId
    ) {
        $this->repeaterService = $repeaterService;
        $this->collectionService = $collectionService;
        $this->request = $request;
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
     * @param int $itemsPerPage
     * @return array
     */
    public function getPaginatedCollection($id, $itemsPerPage = 10)
    {
        $page = $this->request->getQuery('page', 1);

        return $this->repeaterService->fetchPaginated($id, $this->langId, true, true, $page, $itemsPerPage);
    }

    /**
     * Returns collection data by its id
     * 
     * @param int $id Collection id
     * @param array $filter (Alias => Value) filter
     * @return array
     */
    public function getCollection($id, array $filter = [])
    {
        $sortingMethod = $this->collectionService->fetchSortingMethod($id);
        $rows = $this->repeaterService->fetchAll($id, $this->langId, true, $sortingMethod, true);

        if ($filter) {
            $rows = $this->repeaterService->filterRecords($rows, $filter);
        }

        return $rows;
    }
}
