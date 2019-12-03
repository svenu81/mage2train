<?php

namespace E3n\CatalogTags\Controller\Adminhtml\Tag;

use E3n\CatalogTags\Model\Tag;

class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();

        $tagData = $this->getRequest()->getParam('tag');
        if(is_array($tagData)) {
            $tag = $this->_objectManager->create(Tag::class);
            $tag->setData($tagData)->save();
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index');
        }
    }
}
