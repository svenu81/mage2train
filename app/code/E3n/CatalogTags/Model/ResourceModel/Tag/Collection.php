<?php


namespace E3n\CatalogTags\Model\ResourceModel\Tag;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'tag_id';

    protected $_eventPrefix = 'catalogtags_index_grid';

    protected $_eventObject = 'catalogstags_tag_collection';

    protected function _construct()
    {
        $this->_init('E3n\CatalogTags\Model\Tag', 'E3n\CatalogTags\Model\ResourceModel\Tag');
    }


}
