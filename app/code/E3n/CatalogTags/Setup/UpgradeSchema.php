<?php

namespace E3n\CatalogTags\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.1.3', '<')) {
            $this->installCatalogTags($setup, $context);
        }
    }

    protected function installCatalogTags(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $tableName = 'e3n_catalog_tags';
        if ($setup->getConnection()->isTableExists($tableName)) {
            return;
        }

        $table = $setup->getConnection()->newTable($tableName)
            ->addColumn(
                'tag_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Value'
            );
        $setup->getConnection()->createTable($table);
    }
}