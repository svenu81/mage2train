<?php

namespace E3n\CatalogTags\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.1.3', '<')) {
            $this->installCatalogTags($setup, $context);
        }
        if (version_compare($context->getVersion(), '0.1.4', '<')) {
            $this->gimmeMoreTags($setup, $context);
        }
    }

    protected function installCatalogTags(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $tableName = $setup->getTable('e3n_catalog_tags');
        if (!$setup->getConnection()->isTableExists($tableName)) {
            return;
        }

        $select = $setup->getConnection()->select()->from($tableName, 'COUNT(*)');
        if (0 != (int) $setup->getConnection()->fetchOne($select)) {
            return;
        }

        $data = [
            ['value' => 'Tag 1'],
            ['value' => 'Tag 2']
        ];
        foreach ($data as $bind) {
            $setup->getConnection()->insertForce($tableName, $bind);
        }
    }

    protected function gimmeMoreTags(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $tableName = $setup->getTable('e3n_catalog_tags');
        if (!$setup->getConnection()->isTableExists($tableName)) {
            return;
        }

        $data = [
            ['value' => 'Tag 3'],
            ['value' => 'Tag 4'],
            ['value' => 'Tag 5'],
            ['value' => 'Tag 6'],
            ['value' => 'Tag 7'],
            ['value' => 'Tag 8'],
            ['value' => 'Tag 9'],
            ['value' => 'Tag 10'],
            ['value' => 'Tag 11'],
            ['value' => 'Tag 12'],
            ['value' => 'Tag 13'],
            ['value' => 'Tag 14'],
            ['value' => 'Tag 15'],
        ];
        foreach ($data as $bind) {
            $setup->getConnection()->insertForce($tableName, $bind);
        }
    }
}
