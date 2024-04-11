<?php

namespace Structure\Controller;

use Krystal\Application\Controller\AbstractController;

final class API extends AbstractController
{
    /**
     * Fetch data by collection id
     * 
     * @return string JSON output
     */
    public function indexAction()
    {
        $id = $this->request->getQuery('collection_id');

        if ($id) {
            $langId = $this->request->getQuery('lang_id', $this->getService('Cms', 'languageManager')->getDefaultId());
            $rows = $this->getModuleService('siteService')->setLangId($langId)->getCollection($id, $langId);
        } else {
            $rows = [];
        }

        return $this->json($rows);
    }
}
