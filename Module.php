<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Structure;

use Cms\AbstractCmsModule;
use Structure\Service\CollectionService;
use Structure\Service\FieldService;
use Structure\Service\RepeaterService;
use Structure\Service\FileInput;

final class Module extends AbstractCmsModule
{
    /**
     * {@inheritDoc}
     */
    public function getServiceProviders()
    {
        $fileInput = new FileInput($this->appConfig->getRootDir());

        $collectionMapper = $this->getMapper('\Structure\Storage\MySQL\CollectionMapper');
        $fieldMapper = $this->getMapper('\Structure\Storage\MySQL\FieldMapper');
        $repeaterMapper = $this->getMapper('\Structure\Storage\MySQL\RepeaterMapper');
        $valueMapper = $this->getMapper('\Structure\Storage\MySQL\RepeaterValueMapper');

        return array(
            'collectionService' => new CollectionService($collectionMapper),
            'fieldService' => new FieldService($fieldMapper),
            'repeaterService' => new RepeaterService($repeaterMapper, $valueMapper, $fileInput)
        );
    }
}
