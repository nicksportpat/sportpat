<?php
namespace Sportpat\OrderSync\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * install tables
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $this->createSyncedorderTable($setup);
        $installer->endSetup();
    }

    /**
     * install table for Synced Order
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function createSyncedorderTable(SchemaSetupInterface $setup)
    {
        if (!$setup->tableExists('sportpat_order_sync_syncedorder')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('sportpat_order_sync_syncedorder')
            )
            ->addColumn(
                'syncedorder_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Synced Order ID'
            )
            ->addColumn(
                'magento_orderid',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                ],
                'Synced Order Magento Order Id'
            )
            ->addColumn(
                'lightspeed_orderid',
                Table::TYPE_INTEGER,
                null,
                [
                ],
                'Synced Order Lightspeed Order id'
            )
            ->addColumn(
                'status',
                Table::TYPE_INTEGER,
                null,
                [
                ],
                'Synced Order Status'
            )
            ->addColumn(
                'details',
                Table::TYPE_TEXT,
                null,
                [
                ],
                'Synced Order Details'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Synced Order Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Synced Order Updated At'
            )
            ->setComment('Synced Order Table');
            $setup->getConnection()->createTable($table);
            $setup->getConnection()->addIndex(
                $setup->getTable('sportpat_order_sync_syncedorder'),
                $setup->getIdxName(
                    $setup->getTable('sportpat_order_sync_syncedorder'),
                    [
                        'details'
                    ],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    'details'
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
    }
}
