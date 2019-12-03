<?php

namespace E3n\CatalogTags\Model\Tag\Listing;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $field = $filter->getField();
        if (in_array($field, ['id','value'])) {
            $filter->setField($field);
        }
        parent::addFilter($filter);
    }
}