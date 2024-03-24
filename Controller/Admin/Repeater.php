<?php

namespace Structure\Controller\Admin;

use Krystal\Stdlib\VirtualEntity;
use Cms\Controller\Admin\AbstractController;

final class Repeater extends AbstractController
{
    /**
     * Renders repeater
     * 
     * @param string $id Collection id
     * @return string
     */
    public function indexAction($id)
    {
        $collection = $this->getModuleService('collectionService')->fetchById($id);

        if ($collection) {
            // Append breadcrumbs
            $this->view->getBreadcrumbBag()->addOne('Structure', 'Structure:Admin:Collection@indexAction')
                                           ->addOne($this->translator->translate('View fields for "%s" collection', $collection['name']));

            // Grab dynamic fields
            $fields = $this->getModuleService('fieldService')->fetchByCollectionId($id);

            return $this->view->render('repeater', [
                'rows' => $this->getModuleService('repeaterService')->fetchAll($id),
                'fields' => $fields,
                'id' => $id
            ]);

        } else {
            // Invalid collection ID. Trigger 404
            return false;
        }
    }

    /**
     * Renders edit form
     * 
     * @param string $id Repeater id
     * @return string
     */
    public function editAction($id)
    {
        
    }

    /**
     * Deletes a repeater
     * 
     * @param string $id Repeater id
     * @return string
     */
    public function deleteAction($id)
    {
        
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
