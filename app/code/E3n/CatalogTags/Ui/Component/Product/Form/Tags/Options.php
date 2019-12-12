<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace E3n\CatalogTags\Ui\Component\Product\Form\Tags;

use E3n\CatalogTags\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\App\RequestInterface;
use E3n\CatalogTags\Model\Tag as TagModel;

/**
 * Options tree for "Categories" field
 */
class Options implements OptionSourceInterface
{
    /**
     * @var \E3n\CatalogTags\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $tagsTree;

    /**
     * @param TagCollectionFactory $tagCollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        TagCollectionFactory $tagCollectionFactory,
        RequestInterface $request
    ) {
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getTagsTree();
    }

    /**
     * Retrieve tags tree
     *
     * @return array
     */
    protected function getTagsTree()
    {
        if ($this->tagsTree === null) {
            $storeId = $this->request->getParam('store');
            /* @var $matchingNamesCollection \E3n\CatalogTags\Model\ResourceModel\Tag\Collection */
            $matchingNamesCollection = $this->tagCollectionFactory->create();

            $shownTagsIds = [];

            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $collection = $this->tagCollectionFactory->create();

            $tagById = [
                '1' => [
                    'tag_id' => '1'
                ],
            ];

            foreach ($collection as $tag) {
                foreach ([$tag->getTagId()] as $tagId) {
                    if (!isset($tagById[$tagId])) {
                        $tagById[$tagId] = ['tag_id' => $tagId];
                    }
                }

                $tagById[$tag->getTagId()]['value'] = $tag->getValue();
            }

            $this->tagsTree = $tagById;
        }

        return $this->tagsTree;
    }
}
