<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Label\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Amasty\Label\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('am_label'))
            ->addColumn(
                'label_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'pos',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => 0, 'nullable' => false],
                'Position'
            )
            ->addColumn(
                'is_single',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'Is Single'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Name'
            )
            ->addColumn(
                'stores',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Stores'
            )
            ->addColumn(
                'prod_txt',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Product text'
            )
            ->addColumn(
                'prod_img',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Product image'
            )
            ->addColumn(
                'prod_image_size',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Product image size'
            )
            ->addColumn(
                'prod_pos',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'Product position'
            )
            ->addColumn(
                'prod_style',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Product style'
            )
            ->addColumn(
                'prod_text_style',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Product text style'
            )
            ->addColumn(
                'cat_txt',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Category text'
            )
            ->addColumn(
                'cat_img',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Category image'
            )
            ->addColumn(
                'cat_pos',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'Category position'
            )
            ->addColumn(
                'cat_style',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Category style'
            )
            ->addColumn(
                'cat_image_size',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Category image size'
            )
            ->addColumn(
                'cat_text_style',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Category text style'
            )
            ->addColumn(
                'is_new',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'Is new'
            )
            ->addColumn(
                'is_sale',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'Is sale'
            )
            ->addColumn(
                'special_price_only',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'Special Price Only'
            )
            ->addColumn(
                'stock_less',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => 0, 'nullable' => false],
                'Stock less'
            )
            ->addColumn(
                'stock_more',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => 0, 'nullable' => false],
                'Stock more'
            )
            ->addColumn(
                'stock_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => 0, 'nullable' => false],
                'Stock status'
            )
            ->addColumn(
                'from_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'From Date'
            )
            ->addColumn(
                'to_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'To Date'
            )
            ->addColumn(
                'date_range_enabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'Date range enabled'
            )
            ->addColumn(
                'from_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                null,
                ['default' =>  '0.0000', 'nullable' => false],
                'From price'
            )
            ->addColumn(
                'to_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                null,
                ['default' =>  '0.0000', 'nullable' => false],
                'To price'
            )
            ->addColumn(
                'by_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'By price'
            )
            ->addColumn(
                'price_range_enabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'Price range enabled'
            )
            ->addColumn(
                'customer_group_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Customer groups'
            )
            ->addColumn(
                'cond_serialize',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Conditions'
            )
            ->addColumn(
                'customer_group_enabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'Customer group enabled'
            )
            ->addColumn(
                'use_for_parent',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 0, 'nullable' => false],
                'Use for parent'
            );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
