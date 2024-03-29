<?php

namespace Structure\Controller\Admin;

use Krystal\Stdlib\VirtualEntity;
use Cms\Controller\Admin\AbstractController;

final class Repeater extends AbstractController
{
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
            // Load view plugins
            $this->view->getPluginBag()->load($this->getWysiwygPluginName());

            // Append breadcrumbs
            $this->view->getBreadcrumbBag()->addOne('Structure', 'Structure:Admin:Collection@indexAction')
                                           ->addOne($this->translator->translate('View fields for "%s" collection', $collection['name']));

            // Append folder with partials
            $this->view->getPartialBag()
                       ->addPartialDir($this->view->createThemePath($this->moduleName, 'partials'));

            // Grab dynamic fields
            $fields = $this->getModuleService('fieldService')->fetchByCollectionId($collectionId);

            // Override with values
            if ($repeaterId !== null) {
                // Edit mode
                $fields = $this->getModuleService('repeaterService')->appendValues($fields, $repeaterId);
                // Find current repeater
                $repeater = $this->getModuleService('repeaterService')->fetchById($repeaterId);
            }

            // Repeater doesn't exist yet. Create a mock.
            if (!isset($repeater) || !$repeater) {
                $repeater = [
                    'collection_id' => $collectionId
                ];
            }

            // Get current language ID
            $langId = $this->getService('Cms', 'languageManager')->getCurrentId();

            return $this->view->render('repeater', [
                'rows' => $this->getModuleService('repeaterService')->fetchAll($collectionId, $langId),
                'fields' => $fields,
                'repeater' => $repeater,
                'repeaterId' => $repeaterId
            ]);

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

        if (!empty($data['repeater']['id'])) {
            $this->getModuleService('repeaterService')->update($data['repeater']['id'], $data, $files);
            $message = 'Current record has been updated successfully';
        } else {
            $this->getModuleService('repeaterService')->save($data, $files);
            $message = 'New record has been added successfully';
        }

        $this->flashBag->set('success', $message);
        return 1;
    }
}
