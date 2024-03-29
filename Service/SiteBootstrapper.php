<?php

namespace Structure\Service;

use Cms\Service\AbstractSiteBootstrapper;

final class SiteBootstrapper extends AbstractSiteBootstrapper
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap()
    {
        $siteService = $this->moduleManager->getModule('Structure')->getService('siteService');
        $this->view->addVariable('structure', $siteService);
    }
}
