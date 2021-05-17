<?php
namespace Sportpat\Tabcontent\Setup;

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
        $this->createTabcontentTable($setup);
        $this->createTabcontentStoreTable($setup);
        $installer->endSetup();
    }

    /**
     * install table for Manage Content
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function createTabcontentTable(SchemaSetupInterface $setup)
    {
        if (!$setup->tableExists('sportpat_tabcontent_tabcontent')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('sportpat_tabcontent_tabcontent')
            )
            ->addColumn(
                'tabcontent_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Manage Content ID'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                ],
                'Manage Content Title'
            )
            ->addColumn(
                'tab_contenttype',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                ],
                'Manage Content Content Type'
            )
            ->addColumn(
                'content_html',
                Table::TYPE_TEXT,
                '64k',
                [
                ],
                'Manage Content Html / Text Content'
            )
            ->addColumn(
                'image',
                Table::TYPE_TEXT,
                255,
                [
                ],
                'Manage Content Image'
            )
            ->addColumn(
                'for_brand',
                Table::TYPE_INTEGER,
                null,
                [
                ],
                'Manage Content Appears for X Brand'
            )
            ->addColumn(
                'for_category',
                Table::TYPE_INTEGER,
                null,
                [
                ],
                'Manage Content Appears in Category'
            )
            ->addColumn(
                'use_for_skus',
                Table::TYPE_TEXT,
                '64k',
                [
                ],
                'Manage Content Specific Skus'
            )
            ->addColumn(
                'for_gender',
                Table::TYPE_TEXT,
                '64k',
                [
                ],
                'Manage Content Gender'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_INTEGER,
                1,
                [],
                'Manage Content Is Enabled'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Manage Content Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Manage Content Updated At'
            )
            ->setComment('Manage Content Table');
            $setup->getConnection()->createTable($table);
            $setup->getConnection()->addIndex(
                $setup->getTable('sportpat_tabcontent_tabcontent'),
                $setup->getIdxName(
                    $setup->getTable('sportpat_tabcontent_tabcontent'),
                    [
                        'title',
                        'content_html',
                        'image',
                        'use_for_skus'
                    ],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    'title',
                    'content_html',
                    'image',
                    'use_for_skus'
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
    }

    /**
     * install table for Manage Content store link
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
    */
    private function createTabcontentStoreTable(SchemaSetupInterface $setup)
    {
        if (!$setup->tableExists('sportpat_tabcontent_tabcontent_store')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('sportpat_tabcontent_tabcontent_store')
            )->addColumn(
                'tabcontent_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true, 'unsigned' => true],
                'Manage Content ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addIndex(
                $setup->getIdxName('sportpat_tabcontent_tabcontent_store', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $setup->getFkName(
                    'sportpat_tabcontent_tabcontent_store',
                    'tabcontent_id',
                    'sportpat_tabcontent_tabcontent',
                    'tabcontent_id'
                ),
                'tabcontent_id',
                $setup->getTable('sportpat_tabcontent_tabcontent'),
                'tabcontent_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'sportpat_tabcontent_tabcontent_store',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Manage Content To Store Linkage Table');
            $setup->getConnection()->createTable($table);
        }
    }
}
