<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Structure\Controller\Admin;

use Cms\Controller\Admin\AbstractController;

final class Dashboard extends AbstractController
{
    /**
     * Flushes the cache
     * 
     * @return void
     */
    public function flushAction()
    {
        $this->getModuleService('cache')->flush();

        $this->flashBag->set('warning', 'The cache for this module has been cleared successfully');
        $this->response->back();
    }

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