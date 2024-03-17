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
        $input = $this->request->getPost('repeater');
    }
}
