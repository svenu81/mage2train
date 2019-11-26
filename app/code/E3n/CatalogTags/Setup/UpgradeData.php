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
}
