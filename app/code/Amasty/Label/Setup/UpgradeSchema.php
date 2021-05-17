<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Setup;

use Amasty\Base\Helper\Deploy;
use Amasty\Label\Model\ResourceModel\Index;
use Magento\Framework\DB\Ddl\Table as TableDdl;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @package Amasty\Label\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Deploy
     */
    private $pubDeployer;

    /**
     * UpgradeSchema constructor.
     * @param Deploy $pubHelper
     */
    public function __construct(
        Deploy $pubHelper
    ) {
        $this->pubDeployer = $pubHelper;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('am_label'),
                'status',
                TableDdl::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                'Label Status'
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('am_label'),
                'product_stock_enabled',
                TableDdl::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 0],
                'Low stock condition'
            );
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->createExampleLabels($setup, $context);
        }
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->updateNotNullFields($setup);
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->changeColumnsType($setup);
        }
        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $this->addHigherThan($setup);
            $this->updateLessThan($setup);
        }
        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->createIndexTable($setup);
        }
        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $pubPath = __DIR__.'/../pub';
            $this->pubDeployer->deployFolder($pubPath);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param $context
     */
    private function createExampleLabels(SchemaSetupInterface $setup, $context)
    {
        $columns  = ['pos', 'is_single', 'name', 'stores', 'prod_txt', 'prod_img',
            'prod_image_size', 'prod_pos', 'prod_style', 'prod_text_style', 'cat_txt',
            'cat_img', 'cat_pos', 'cat_style', 'cat_image_size', 'cat_text_style',
            'is_new', 'is_sale', 'special_price_only', 'stock_less', 'stock_more',
            'stock_status', 'from_date', 'to_date', 'date_range_enabled', 'from_price',
            'to_price', 'by_price', 'price_range_enabled', 'customer_group_ids',
            'cond_serialize', 'customer_group_enabled', 'use_for_parent', 'status', 'product_stock_enabled'];

        $setup->getConnection()->insertArray(
            $setup->getTable('am_label'),
            $columns,
            [
                [
                    0, 0, 'New Label', '1', '', 'new-arrival.png', '', 0, 'margin: 5px;', '', '', 'new-green.png',
                    2, '', '', '', 2, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0', '0', 0, 0,
                    '', '', 0, 0, 0, 0
                ],
                [
                    2, 0, 'On Sale Label', '1', '', 'sale-red.png', '', 0, '', '', 'Sale', 'label-red.png', 2,
                    'font-size: 14px;color: #ffffff;', '', '', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00',
                    '0000-00-00 00:00:00', 0, '0', '0', 0, 0, '',
                    '', 0, 0, 0, 0
                ],
                [
                    0, 0, 'Out Of Stock Label', '1', 'Out Of Stock', 'out_of_stock_label.svg', '40', 0, '', '',
                    'Out Of Stock', 'out_of_stock_label.svg', 0, '', '40', '', 0, 0, 0  , 0, 0, 1,
                    '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0', '0', 0, 0, '', '', 0, 0, 0, 0
                ]
            ]
        );
    }

    /**
     * update to_date and from_date to save null if empty fields
     * @param SchemaSetupInterface $setup
     */
    private function updateNotNullFields(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->changeColumn(
            $setup->getTable('am_label'),
            'to_date',
            'to_date',
            ['type' => Table::TYPE_DATETIME, 'nullable' => true],
            'To Date'
        );
        $setup->getConnection()->changeColumn(
            $setup->getTable('am_label'),
            'from_date',
            'from_date',
            ['type' => Table::TYPE_DATETIME, 'nullable' => true],
            'From Date'
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function changeColumnsType(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->changeColumn(
                $setup->getTable('am_label'),
                'from_price',
                'from_price',
                [
                    'type' => Table::TYPE_FLOAT,
                    'length' => '10,4',
                    'nullable' => false,
                    'comment' => 'From price'
                ]
            );
        $setup->getConnection()
            ->changeColumn(
                $setup->getTable('am_label'),
                'to_price',
                'to_price',
                [
                    'type' => Table::TYPE_FLOAT,
                    'length' => '10,4',
                    'nullable' => false,
                    'comment' => 'To price'
                ]
            );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addHigherThan(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('am_label'),
            'stock_higher',
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => true,
                'default' => null
            ],
            'Stock higher'
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function updateLessThan(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->changeColumn(
            $setup->getTable('am_label'),
            'stock_less',
            'stock_less',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Stock less',
                'default' => null
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createIndexTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(Index::AMASTY_LABEL_INDEX_TABLE)
        )->addColumn(
            'index_id',
            TableDdl::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Index Id'
        )->addColumn(
            'label_id',
            TableDdl::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Label Id'
        )->addColumn(
            'product_id',
            TableDdl::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Product Id'
        )->addColumn(
            'store_id',
            TableDdl::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Store Id'
        )->addIndex(
            $installer->getIdxName(
                Index::AMASTY_LABEL_INDEX_TABLE,
                [
                    'label_id',
                    'product_id',
                    'store_id'
                ],
                true
            ),
            [
                'label_id',
                'product_id',
                'store_id'
            ],
            ['type' => 'unique']
        )->addIndex(
            $installer->getIdxName(Index::AMASTY_LABEL_INDEX_TABLE, ['label_id']),
            ['label_id']
        )->addIndex(
            $installer->getIdxName(Index::AMASTY_LABEL_INDEX_TABLE, ['product_id']),
            ['product_id']
        )->addIndex(
            $installer->getIdxName(Index::AMASTY_LABEL_INDEX_TABLE, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName(
                Index::AMASTY_LABEL_INDEX_TABLE,
                'label_id',
                'am_label',
                'label_id'
            ),
            'label_id',
            $installer->getTable('am_label'),
            'label_id',
            TableDdl::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                Index::AMASTY_LABEL_INDEX_TABLE,
                'product_id',
                'catalog_product_entity',
                'entity_id'
            ),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            TableDdl::ACTION_CASCADE
        )->setComment(
            'Amasty Label Index'
        );

        $installer->getConnection()->createTable($table);
    }
}
