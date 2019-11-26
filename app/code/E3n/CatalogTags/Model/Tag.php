<?php

namespace E3n\CatalogTags\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Tag extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'e3n_catalogtags_tag';

    protected $_cacheTag = 'e3n_catalogtags_tag';

    protected $_eventPrefix = 'e3n_catalogstags_tag';

    protected function _construct()
    {
        $this->_init('E3n\CatalogTags\Model\ResourceModel\Tag');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        return [];
    }
}