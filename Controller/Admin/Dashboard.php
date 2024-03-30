<?php

namespace Structure\Controller\Admin;

use Cms\Controller\Admin\AbstractController;

final class Dashboard extends AbstractController
{
    /**
     * Renders dashboard
     * 
     * @return string
     */
    public function indexAction()
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Structure');

        return $this->view->render('dashboard', [
            'collections' => $this->getModuleService('collectionService')->fetchAll(true)
        ]);
    }
}
