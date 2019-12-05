<?php

namespace E3n\CatalogTags\Ui\DataProvider\Product\Form\Modifier;

use E3n\CatalogTags\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;
use E3n\CatalogTags\Model\Tag;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;

class TagSelection extends AbstractModifier
{
    /**#@+
     * Category tree cache id
     */
    const TAG_TREE_ID = 'TAG_PRODUCT_TAG_TREE';
    /**#@-*/

    /**
     * @var TagCollectionFactory
     * @since 101.0.0
     */
    protected $tagCollectionFactory;

    /**
     * @var DbHelper
     * @since 101.0.0
     */
    protected $dbHelper;

    /**
     * @var array
     * @deprecated 101.0.0
     * @since 101.0.0
     */
    protected $tagTrees = [];

    /**
     * @var LocatorInterface
     * @since 101.0.0
     */
    protected $locator;

    /**
     * @var UrlInterface
     * @since 101.0.0
     */
    protected $urlBuilder;

    /**
     * @var ArrayManager
     * @since 101.0.0
     */
    protected $arrayManager;

    /**
     * @var CacheInterface
     */
    private $cacheManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        LocatorInterface $locator,
        TagCollectionFactory $tagCollectionFactory,
        DbHelper $dbHelper,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        SerializerInterface $serializer = null
    ) {
        $this->locator = $locator;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->dbHelper = $dbHelper;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(SerializerInterface::class);
    }

    /**
     * @inheritdoc
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->createNewTagModal($meta);
        $meta = $this->customizeTagsField($meta);

        return $meta;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    protected function createNewTagModal(array $meta)
    {
        return $this->arrayManager->set(
            'create_tag_modal',
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'isTemplate' => false,
                            'componentType' => 'modal',
                            'options' => [
                                'title' => __('New Tag'),
                            ],
                            'imports' => [
                                'state' => '!index=create_tag:responseStatus'
                            ],
                        ],
                    ],
                ],
                'children' => [
                    'create_tag' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => '',
                                    'componentType' => 'container',
                                    'component' => 'Magento_Ui/js/form/components/insert-form',
                                    'dataScope' => '',
                                    'update_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                    'render_url' => $this->urlBuilder->getUrl(
                                        'mui/index/render_handle',
                                        [
                                            'handle' => 'catalog_tag_create',
                                            'store' => $this->locator->getStore()->getId(),
                                            'buttons' => 1
                                        ]
                                    ),
                                    'autoRender' => false,
                                    'ns' => 'new_tag_form',
                                    'externalProvider' => 'new_tag_form.new_tag_form_data_source',
                                    'toolbarContainer' => '${ $.parentName }',
                                    'formSubmitType' => 'ajax',
                                ],
                            ],
                        ]
                    ]
                ]
            ]
        );
    }

    protected function customizeTagsField(array $meta)
    {
        $fieldCode = 'tag_ids';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');

        if (!$elementPath) {
            return $meta;
        }

        $value = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Tags'),
                        'dataScope' => '',
                        'breakLine' => false,
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'component' => 'Magento_Ui/js/form/components/group',
                        'scopeLabel' => __('[GLOBAL]'),
                        'disabled' => $this->locator->getProduct()->isLockedAttribute($fieldCode),
                    ],
                ],
            ],
            'children' => [
                $fieldCode => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'select',
                                'componentType' => 'field',
                                'component' => 'Magento_Catalog/js/components/new-category',
                                'filterOptions' => true,
                                'chipsEnabled' => true,
                                'disableLabel' => true,
                                'levelsVisibility' => '1',
                                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                'options' => $this->getCategoriesTree(),
                                'listens' => [
                                    'index=create_tag:responseData' => 'setParsed',
                                    'newOption' => 'toggleOptionSelected'
                                ],
                                'config' => [
                                    'dataScope' => $fieldCode,
                                    'sortOrder' => 10,
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];

        $value['children']['create_tag_button'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'title' => __('New Tag'),
                        'formElement' => 'container',
                        'additionalClasses' => 'admin__field-small',
                        'componentType' => 'container',
                        'component' => 'Magento_Ui/js/form/components/button',
                        'template' => 'ui/form/components/button/container',
                        'actions' => [
                            [
                                'targetName' => 'product_form.product_form.create_tag_modal',
                                'actionName' => 'toggleModal',
                            ],
                            [
                                'targetName' => 'product_form.product_form.create_tag_modal.create_tag',
                                'actionName' => 'render'
                            ],
                            [
                                'targetName' => 'product_form.product_form.create_tag_modal.create_tag',
                                'actionName' => 'resetForm'
                            ]
                        ],
                        'additionalForGroup' => true,
                        'provider' => false,
                        'source' => 'product_details',
                        'displayArea' => 'insideGroup',
                        'sortOrder' => 15,
                        'dataScope'  => $fieldCode,
                    ],
                ],
            ]
        ];

        $meta = $this->arrayManager->merge(
            $containerPath,
            $meta,
            $value
        );

        return $meta;
    }

    protected function getTagsTree($filter = null)
    {
        $storeId = (int) $this->locator->getStore()->getId();

        $tagsTree = $this->retrieveTagsTree(
            $storeId,
            $this->retrieveShownTagsIds($storeId, (string) $filter)
        );

        return $tagsTree;
    }

    private function retrieveShownTagsIds(int $storeId, string $filter = '') : array
    {
        /* @var $matchingNamesCollection \E3n\CatalogTags\Model\ResourceModel\Tag\Collection */
        $matchingNamesCollection = $this->tagCollectionFactory->create();

        if (!empty($filter)) {
            $matchingNamesCollection->addAttributeToFilter(
                'name',
                ['like' => $this->dbHelper->addLikeEscape($filter, ['position' => 'any'])]
            );
        }

        $matchingNamesCollection->addAttributeToSelect('path')
            //->addAttributeToFilter('entity_id', ['neq' => CategoryModel::TREE_ROOT_ID])
            ->setStoreId($storeId);

        $shownTagsIds = [];

        /** @var \E3n\CatalogTags\Model\Tag $tag */
        foreach ($matchingNamesCollection as $tag) {
            foreach (explode('/', $tag->getValue()) as $parentId) {
                $shownTagsIds[$parentId] = 1;
            }
        }

        return $shownTagsIds;
    }

    private function retrieveTagsTree(int $storeId, array $shownTagsIds) : ?array
    {
        /* @var $collection \E3n\CatalogTags\Model\ResourceModel\Tag\Collection */
        $collection = $this->tagCollectionFactory->create();

        $collection->addAttributeToFilter('entity_id', ['in' => array_keys($shownTagsIds)])
            //->addAttributeToSelect(['name', 'is_active', 'parent_id'])
            ->setStoreId($storeId);

        $tagById = [
            Tag::TREE_ROOT_ID => [
                'value' => Tag::TREE_ROOT_ID,
                'optgroup' => null,
            ],
        ];

        foreach ($collection as $tag) {
            foreach ([$tag->getId(), $tag->getParentId()] as $tagId) {
                if (!isset($tagById[$tagId])) {
                    $tagById[$tagId] = ['value' => $tagId];
                }
            }

            $tagById[$tag->getId()]['value'] = $tag->getIsActive();
            $tagById[$tag->getId()]['__disableTmpl'] = true;
            $tagById[$tag->getParentId()]['optgroup'][] = &$tagById[$tag->getId()];
        }

        return $tagById[Tag::TREE_ROOT_ID]['optgroup'];
    }
}
