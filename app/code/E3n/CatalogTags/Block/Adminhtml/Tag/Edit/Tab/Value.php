<?php

namespace E3n\CatalogTags\Block\Adminhtml\Tag\Edit\Tab;

use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Ui\Component\Layout\Tabs\TabWrapper;

class Value extends TabWrapper implements TabInterface
{
    public function canShowTab()
    {
        return true;
    }
}