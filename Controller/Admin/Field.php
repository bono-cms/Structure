<?php

namespace Structure\Controller\Admin;

use Krystal\Stdlib\VirtualEntity;
use Cms\Controller\Admin\AbstractController;

final class Field extends AbstractController
{
    /**
     * Renders the form
     * 
     * @param array $collection
     * @param array $field
     * @return string
     */
    private function renderForm(array $collection, $field)
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Structure', 'Structure:Admin:Dashboard@indexAction')
                                       ->addOne($this->translator->translate('View fields for "%s" collection', $collection['name']));

        $fieldService = $this->getModuleService('fieldService');

        return $this->view->render('fields', [
            'fields' => $fieldService->fetchByCollectionId($collection['id'], false),
            'field' => $field
        ]);
    }

    /**
     * Render all fields by collection id
     * 
     * @param mixed $id Collection id
     * @return string
     */
    public function indexAction($id = null)
    {
        $collectionService = $this->getModuleService('collectionService');
        $collection = $collectionService->fetchById($id);

        if ($collection) {
            $field = new VirtualEntity();
            $field->setCollectionId($id);

            return $this->renderForm($collection, $field);
        } else {
            // Invalid collection id. Trigger 404
            return false;
        }
    }

    /**
     * Render edit form
     * 
     * @param string $id Field id
     * @return string
     */
    public function editAction($id)
    {
        $field = $this->getModuleService('fieldService')->fetchById($id);

        if ($field) {
            $collection = $this->getModuleService('collectionService')->fetchById($field['collection_id']);
            return $this->renderForm($collection, $field);
        }
    }

    /**
     * Saves a field
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $input = $this->request->getPost('field');

        $fieldService = $this->getModuleService('fieldService');
        $fieldService->save($input);

        if ($input['id']) {
            $this->flashBag->set('success', 'The field has been updated successfully');
            return 1;
        } else {
            $this->flashBag->set('success', 'The field has been created successfully');
            return $fieldService->getLastId();
        }
    }

    /**
     * Deletes a field by its id
     * 
     * @param string $id
     * @return mixed
     */
    public function deleteAction($id)
    {
        // Delete filest first
        $this->getModuleService('repeaterService')->deleteFilesByFieldId($id);

        $this->getModuleService('fieldService')->deleteById($id);
        $this->flashBag->set('success', 'Selected field has been deleted successfully');

        return 1;
    }
}
