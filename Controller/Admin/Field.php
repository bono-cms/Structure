<?php

namespace Structure\Controller\Admin;

use Krystal\Validate\Pattern;
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
            $field->setGridable(true);

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
        } else {
            return false;
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

        // Construct form validator
        $formValidator = $this->createValidator([
            'input' => [
                'source' => $input,
                'definition' => [
                    'name' => [
                        'required' => true,
                        'rules' => [
                            'Unique' => [
                                'value' => $fieldService->nameExists($input['collection_id'], $input['name']) && !$input['id'],
                                'message' => 'This name is already taken'
                            ]
                        ]
                    ],
                    'alias' => [
                        'required' => true,
                        'rules' => [
                            'Unique' => [
                                'value' => $fieldService->aliasExists($input['collection_id'], $input['alias']) && !$input['id'],
                                'message' => 'This alias is already taken'
                            ],
                            'NotEquals' => [
                                'value' => 'id',
                                'message' => 'An alias can not contain reserved keyword `id`'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        // Stop, if form invalid
        if (!$formValidator->isValid()) {
            return $formValidator->getErrors();
        }

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
