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
use Structure\Collection\SortingCollection;
use Structure\Collection\LayoutCollection;

final class Collection extends AbstractController
{
    /**
     * Truncates collection
     * 
     * @param mixed $id Collection id
     * @return string
     */
    public function truncateAction($id)
    {
        $repeaterService = $this->getModuleService('repeaterService');

        $repeaterService->deleteFilesByCollectionId($id);
        $repeaterService->truncateByCollectionId($id);

        $this->flashBag->set('success', 'Selected collection has been truncated successfully');

        return $this->json([
            'refresh' => true
        ]);
    }

    /**
     * Renders main grid
     * 
     * @param mixed $id Collection id
     * @return string
     */
    public function indexAction($id = null)
    {
        $collectionService = $this->getModuleService('collectionService');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Structure', 'Structure:Admin:Dashboard@indexAction')
                                       ->addOne('View collections');

        if ($id === null) {
            // Configure defaults
            $collection = new VirtualEntity();
            $collection->setLayout(LayoutCollection::LAYOUT_LEFT_GRID_RIGHT_FORM)
                       ->setSortingMethod(SortingCollection::SORTING_BY_ORDER);

        } else {
            $collection = $collectionService->fetchById($id);

            // Could not find? Throw 404
            if (!$collection) {
                return false;
            }

            // Avoid fetching fields, if sorting method doesn't require them
            if (SortingCollection::isCustomSorting($collection['sorting_method'])) {
                $fields = $this->getModuleService('fieldService')->fetchFields($id);
            }
        }

        return $this->view->render('collection', [
            'fields' => isset($fields) ? $fields : [],
            'sortingOptions' => (new SortingCollection)->getAll(),
            'collection' => $collection,
            'collections' => $collectionService->fetchAll(false)
        ]);
    }

    /**
     * Saves a collection
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('collection');

        $validator = $this->createValidation();

        $validator->field('collection.name')
                  ->required();

        if (!$validator->isPassed()) {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }

        $collectionService = $this->getModuleService('collectionService');
        $collectionService->save($input);

        if ($input['id']) {
            $this->flashBag->set('success', 'The collection has been updated successfully');

            return $this->json([
                'refresh' => true
            ]);
        } else {
            $this->flashBag->set('success', 'The collection has been created successfully');

            return $this->json([
                'redirect' => $this->createUrl('Structure:Admin:Collection@editAction', [$collectionService->getLastId()]),
            ]);
        }
    }

    /**
     * Render edit form
     * 
     * @param string $id Collection id
     * @return string
     */
    public function editAction($id)
    {
        return $this->indexAction($id);
    }

    /**
     * Deletes a collection by its id
     * 
     * @param string $id Collection id
     * @return string
     */
    public function deleteAction($id)
    {
        // Delete files first
        $this->getModuleService('repeaterService')->deleteFilesByCollectionId($id);

        // Delete collection last
        $this->getModuleService('collectionService')->deleteByPk($id);

        $this->flashBag->set('success', 'Selected collection has been deleted successfully');

        return $this->json([
            'refresh' => true
        ]);
    }
}