<?php
namespace Sportpat\HomeBanner\Setup;

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
        $this->createBannerTable($setup);
        $this->createBannerStoreTable($setup);
        $installer->endSetup();
    }

    /**
     * install table for Banner
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function createBannerTable(SchemaSetupInterface $setup)
    {
        if (!$setup->tableExists('sportpat_home_banner_banner')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('sportpat_home_banner_banner')
            )
            ->addColumn(
                'banner_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Banner ID'
            )
            ->addColumn(
                'banner_name',
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                ],
                'Banner Banner name (not visible)'
            )
            ->addColumn(
                'banner_image',
                Table::TYPE_TEXT,
                255,
                [
                ],
                'Banner Banner image'
            )
            ->addColumn(
                'banner_link',
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                ],
                'Banner Banner link to'
            )
            ->addColumn(
                'banner_width',
                Table::TYPE_TEXT,
                null,
                [
                ],
                'Banner Banner width'
            )
            ->addColumn(
                'banner_height',
                Table::TYPE_TEXT,
                null,
                [
                ],
                'Banner Banner height'
            )
            ->addColumn(
                'banner_size',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                ],
                'Banner Banner size'
            )
            ->addColumn(
                'banner_order',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                ],
                'Banner Banner order'
            )
            ->addColumn(
                'banner_showfromdate',
                Table::TYPE_DATETIME,
                null,
                [
                ],
                'Banner Show from date'
            )
            ->addColumn(
                'banner_showtodate',
                Table::TYPE_DATETIME,
                null,
                [
                ],
                'Banner Stop showing on'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_INTEGER,
                1,
                [],
                'Banner Is Enabled'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Banner Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Banner Updated At'
            )
            ->setComment('Banner Table');
            $setup->getConnection()->createTable($table);
            $setup->getConnection()->addIndex(
                $setup->getTable('sportpat_home_banner_banner'),
                $setup->getIdxName(
                    $setup->getTable('sportpat_home_banner_banner'),
                    [
                        'banner_name',
                        'banner_image',
                        'banner_link',
                        'banner_width',
                        'banner_height'
                    ],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    'banner_name',
                    'banner_image',
                    'banner_link',
                    'banner_width',
                    'banner_height'
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
    }

    /**
     * install table for Banner store link
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
    */
    private function createBannerStoreTable(SchemaSetupInterface $setup)
    {
        if (!$setup->tableExists('sportpat_home_banner_banner_store')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('sportpat_home_banner_banner_store')
            )->addColumn(
                'banner_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true, 'unsigned' => true],
                'Banner ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addIndex(
                $setup->getIdxName('sportpat_home_banner_banner_store', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $setup->getFkName(
                    'sportpat_home_banner_banner_store',
                    'banner_id',
                    'sportpat_home_banner_banner',
                    'banner_id'
                ),
                'banner_id',
                $setup->getTable('sportpat_home_banner_banner'),
                'banner_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'sportpat_home_banner_banner_store',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Banner To Store Linkage Table');
            $setup->getConnection()->createTable($table);
        }
    }
}
