<?php

namespace Structure\Controller\Admin;

use Krystal\Stdlib\VirtualEntity;
use Cms\Controller\Admin\AbstractController;

final class Repeater extends AbstractController
{
    /**
     * Find repeaters depending on per page count
     * 
     * @param int $collectionId
     * @return array
     */
    private function findRepeaters($collectionId)
    {
        // Grab current configuration
        $config = $this->moduleManager->getModule($this->moduleName)->getConfig();
        $perPageCount = isset($config['perPageCount']) ? $config['perPageCount'] : null;

        // Current page number
        $page = (int) $this->request->getQuery('page', 1);

        // Get current language ID
        $langId = $this->getService('Cms', 'languageManager')->getCurrentId();
        $repeaterService = $this->getModuleService('repeaterService');

        if ($perPageCount !== null) {
            return [
                'pageNumber' => $page,
                'rows' => $repeaterService->fetchPaginated($collectionId, $langId, false, false, $page, $perPageCount),
                'count' => $repeaterService->countRepeaters($collectionId),
                'paginator' => $repeaterService->getPaginator()
            ];
        } else {
            $rows = $repeaterService->fetchAll($collectionId, $langId);

            return [
                'pageNumber' => $page,
                'rows' => $rows,
                'count' => count($rows)
            ];
        }
    }

    /**
     * Renders repeater by collection ID
     * 
     * @param string $collectionId
     * @param string $repeaterId
     * @return string
     */
    public function indexAction($collectionId, $repeaterId = null)
    {
        $collection = $this->getModuleService('collectionService')->fetchById($collectionId);

        if ($collection) {
            $repeaterService = $this->getModuleService('repeaterService');
            
            // Load view plugins
            $this->view->getPluginBag()->load($this->getWysiwygPluginName());

            // Append breadcrumbs
            $this->view->getBreadcrumbBag()->addOne('Structure', 'Structure:Admin:Dashboard@indexAction')
                                           ->addOne($this->translator->translate('View fields for "%s" collection', $collection['name']));

            // Append folder with partials
            $this->view->getPartialBag()
                       ->addPartialDir($this->view->createThemePath($this->moduleName, 'partials'));

            // Grab dynamic fields
            $fields = $this->getModuleService('fieldService')->fetchByCollectionId($collectionId, true);

            // Override with values
            if ($repeaterId !== null) {
                // Edit mode
                $fields = $repeaterService->appendValues($fields, $repeaterId);
                // Find current repeater
                $repeater = $repeaterService->fetchById($repeaterId);
            }

            // Repeater doesn't exist yet. Create a mock.
            if (!isset($repeater) || !$repeater) {
                $repeater = [
                    'collection_id' => $collectionId
                ];
            }

            return $this->view->render('repeater', array_merge($this->findRepeaters($collectionId), [
                'collection' => $collection,
                'fields' => $fields,
                'repeater' => $repeater,
                'repeaterId' => $repeaterId
            ]));

        } else {
            // Invalid collection ID. Trigger 404
            return false;
        }
    }

    /**
     * Renders edit form
     * 
     * @param string $collectionId
     * @param string $repeaterId
     * @return string
     */
    public function editAction($collectionId, $repeaterId)
    {
        // Fix issue with mixed query string, if detected
        if (!is_numeric($repeaterId)) {
            $url = parse_url($repeaterId);

            if (isset($url['path']) && is_numeric($url['path'])){
                $repeaterId = $url['path'];
            }
        }

        return $this->indexAction($collectionId, $repeaterId);
    }

    /**
     * Deletes a repeater
     * 
     * @param string $id Repeater id
     * @return string
     */
    public function deleteAction($id)
    {
        $repeaterService = $this->getModuleService('repeaterService');

        $repeaterService->deleteFilesByRepeaterId($id);
        $repeaterService->deleteById($id);

        $this->flashBag->set('success', 'Selected record has been deleted successfully');
        return 1;
    }

    /**
     * Persists a repeater
     * 
     * @return string
     */
    public function saveAction()
    {
        // All request data
        $input = $this->request->getAll();

        $data = $input['data'];
        $files = isset($input['files']) ? $input['files'] : []; // Grab files, if found

        // Repeater service
        $repeaterService = $this->getModuleService('repeaterService');

        // Update
        if (!empty($data['repeater']['id'])) {
            $repeaterService->update($data['repeater']['id'], $data, $files);
            $this->flashBag->set('success', 'Current record has been updated successfully');
            return 1;
        } else {
            // Create
            $this->getModuleService('repeaterService')->save($data, $files);
            $this->flashBag->set('success', 'New record has been added successfully');

            return $repeaterService->getLastId();
        }
    }
}
