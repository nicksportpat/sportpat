<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V121\UpgradeSchema\ReviewImageTable;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Api\Data\Source\Table\ReviewInterface;
use Aitoc\ReviewBooster\Api\Setup\V121\ReviewImageTableInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class Create
 */
class Create implements UpgradeSchemaOperationInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ReviewImageTableInterface::TABLE_NAME))
            ->addColumn(
                ReviewImageTableInterface::COLUMN_NAME_IMAGE_ID,
                Table::TYPE_BIGINT,
                20,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Image id'
            )
            ->addColumn(
                ReviewImageTableInterface::COLUMN_NAME_REVIEW_ID,
                Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Review id'
            )
            ->addColumn(
                ReviewImageTableInterface::COLUMN_NAME_IMAGE,
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => true, 'default' => null],
                'Image'
            )
            ->setComment(
                'Images Collection'
            )->addForeignKey(
                $setup->getFkName(
                    $setup->getTable(ReviewImageTableInterface::TABLE_NAME),
                    ReviewImageTableInterface::COLUMN_NAME_REVIEW_ID,
                    $setup->getTable(ReviewInterface::TABLE_NAME),
                    ReviewInterface::COLUMN_NAME_REVIEW_ID
                ),
                ReviewImageTableInterface::COLUMN_NAME_REVIEW_ID,
                $setup->getTable(ReviewInterface::TABLE_NAME),
                ReviewInterface::COLUMN_NAME_REVIEW_ID,
                AdapterInterface::FK_ACTION_CASCADE
            );

        $setup->getConnection()->createTable($table);
    }
}
