<?php

namespace Scommerce\GoogleUniversalAnalytics\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'google_ts_cookie',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Traffic Source Cookie',
                ]
            );
        }
		if (version_compare($context->getVersion(), '2.0.13') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'google_category',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Google Category Enhanced Ecommerce',
                ]
            );
			
			$installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'google_category',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Google Category Enhanced Ecommerce',
                ]
            );
        }

        $setup->endSetup();
    }
}