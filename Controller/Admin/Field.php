<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

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
     * @return string
     */
    public function saveAction()
    {
        $this->getModuleService('cache')->flush();

        $input = $this->request->getPost('field');
        $fieldService = $this->getModuleService('fieldService');

        $validator = $this->createValidation();

        // Register custom uniqueness rules under non-conflicting names to avoid
        // overriding the built-in "unique" field validation rule.
        $validator->setFieldRule('uniqueName', function ($value, array $options, array $data) use ($input, $fieldService) {
            if (!empty($input['id'])) {
                return true;
            }
            return !$fieldService->nameExists($input['collection_id'], $value);
        }, 'This name is already taken');

        $validator->setFieldRule('uniqueAlias', function ($value, array $options, array $data) use ($input, $fieldService) {
            if (!empty($input['id'])) {
                return true;
            }
            return !$fieldService->aliasExists($input['collection_id'], $value);
        }, 'This alias is already taken');

        $validator->field('field.name')
                  ->required('Name can not be empty')
                  ->addRule('uniqueName');

        $validator->field('field.alias')
                  ->required('Alias can not be empty')
                  ->addRule('uniqueAlias')
                  ->addRule('notequals', 'An alias can not contain reserved keyword `id`', ['value' => 'id'])
                  ->addRule('regex', 'Ensure that the alias does not contain spaces or dashes', ['pattern' => '/^[^\s\-]+$/']);

        if (!$validator->isPassed()) {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }

        $fieldService->save($input);

        if ($input['id']) {
            $this->flashBag->set('success', 'The field has been updated successfully');

            return $this->json([
                'refresh' => true
            ]);
        } else {
            $this->flashBag->set('success', 'The field has been created successfully');

            return $this->json([
                'redirect' => $this->createUrl('Structure:Admin:Field@editAction', [$fieldService->getLastId()]),
            ]);
        }
    }

    /**
     * Deletes a field by its id
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        $this->getModuleService('cache')->flush();

        // Delete files first
        $this->getModuleService('repeaterService')->deleteFilesByFieldId($id);
        $this->getModuleService('fieldService')->deleteById($id);

        $this->flashBag->set('success', 'Selected field has been deleted successfully');

        return $this->json([
            'refresh' => true
        ]);
    }
}
