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

            // Grab dynamic fields
            $fields = $this->getModuleService('fieldService')->fetchByCollectionId($collectionId);

            // Override with values
            if ($repeaterId !== null) {
                // Edit mode
                $fields = $this->getModuleService('repeaterService')->appendValues($fields, $repeaterId);
                // Find current repeater
                $repeater = $this->getModuleService('repeaterService')->fetchById($repeaterId);
            } else {
                // Repeater doesn't exist yet. Create a mock.
                $repeater = [
                    'collection_id' => $collectionId
                ];
            }

            return $this->view->render('repeater', [
                'rows' => $this->getModuleService('repeaterService')->fetchAll($collectionId),
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
        $this->getModuleService('repeaterService')->deleteById($id);
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
        $input = $this->request->getPost();

        if (!empty($input['repeater']['id'])) {
            $this->getModuleService('repeaterService')->update($input['repeater']['id'], $input);
            $message = 'Current record has been updated successfully';
        } else {
            $this->getModuleService('repeaterService')->save($input);
            $message = 'New record has been added successfully';
        }
        
        $this->flashBag->set('success', $message);
        return 1;
    }
}
