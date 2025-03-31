<?php

namespace Structure\Controller\Admin;

use Krystal\Stdlib\VirtualEntity;
use Cms\Controller\Admin\AbstractController;
use Structure\Collection\SortingCollection;

final class Collection extends AbstractController
{
    /**
     * Truncates collection
     * 
     * @param mixed $id Collection id
     * @return void
     */
    public function truncateAction($id)
    {
        $repeaterService = $this->getModuleService('repeaterService');

        $repeaterService->deleteFilesByCollectionId($id);
        $repeaterService->truncateByCollectionId($id);

        $this->flashBag->set('success', 'Selected collection has been truncated successfully');
        return 1;
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
            $collection = new VirtualEntity();
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
     * @return mixed
     */
    public function saveAction()
    {
        $input = $this->request->getPost('collection');

        $collectionService = $this->getModuleService('collectionService');
        $collectionService->save($input);

        if ($input['id']) {
            $this->flashBag->set('success', 'The collection has been updated successfully');
            return 1;
        } else {
            $this->flashBag->set('success', 'The collection has been created successfully');
            return $collectionService->getLastId();
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
     * @return mixed
     */
    public function deleteAction($id)
    {
        // Delete filest first
        $this->getModuleService('repeaterService')->deleteFilesByCollectionId($id);

        // Delete collection last
        $this->getModuleService('collectionService')->deleteByPk($id);

        $this->flashBag->set('success', 'Selected collection has been deleted successfully');
        return 1;
    }
}
