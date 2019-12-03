<?php

namespace E3n\CatalogTags\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Tag extends AbstractDb
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function _construct()
    {
        $this->_init('e3n_catalog_tags', 'tag_id');
    }
}
