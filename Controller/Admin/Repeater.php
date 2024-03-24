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
            // Append breadcrumbs
            $this->view->getBreadcrumbBag()->addOne('Structure', 'Structure:Admin:Collection@indexAction')
                                           ->addOne($this->translator->translate('View fields for "%s" collection', $collection['name']));

            // Grab dynamic fields
            $fields = $this->getModuleService('fieldService')->fetchByCollectionId($collectionId);

            // Override with values, 
            if ($repeaterId !== null) {
                $fields = $this->getModuleService('repeaterService')->appendValues($fields, $repeaterId);
            }

            return $this->view->render('repeater', [
                'rows' => $this->getModuleService('repeaterService')->fetchAll($collectionId),
                'fields' => $fields,
                'repeaterId' => $repeaterId,
                'collectionId' => $collectionId
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
        $this->getModuleService('repeaterService')->save($input);

        $this->flashBag->set('success', 'New record has been added successfully');
        return 1;
    }
}
