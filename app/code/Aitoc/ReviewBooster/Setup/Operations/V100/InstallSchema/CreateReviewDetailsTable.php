<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V100\InstallSchema;

use Aitoc\FollowUpEmails\Api\Setup\InstallSchemaOperationInterface;
use Aitoc\ReviewBooster\Api\Data\Source\Table\ReviewInterface;
use Aitoc\ReviewBooster\Api\Setup\V100\ReviewDetailsTableInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class CreateReviewDetailsTable
 */
class CreateReviewDetailsTable implements InstallSchemaOperationInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ReviewDetailsTableInterface::TABLE_NAME))
            ->addColumn(
                ReviewDetailsTableInterface::COLUMN_NAME_ID,
                Table::TYPE_BIGINT,
                20,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Review extended id'
            )
            ->addColumn(
                ReviewDetailsTableInterface::COLUMN_NAME_REVIEW_ID,
                Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Review id'
            )
            ->addColumn(
                ReviewDetailsTableInterface::COLUMN_NAME_PRODUCT_ADVANTAGES,
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => true, 'default' => null],
                'Product advantages'
            )
            ->addColumn(
                ReviewDetailsTableInterface::COLUMN_NAME_PRODUCT_DISADVANTAGES,
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => true, 'default' => null],
                'Product disadvantages'
            )
            ->addColumn(
                ReviewDetailsTableInterface::COLUMN_NAME_REVIEW_HELPFUL,
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Review helpful'
            )
            ->addColumn(
                ReviewDetailsTableInterface::COLUMN_NAME_REVIEW_UNHELPFUL,
                Table::TYPE_INTEGER,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Review unhelpful'
            )
            ->addColumn(
                ReviewDetailsTableInterface::COLUMN_NAME_REVIEW_REPORTED,
                Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Is review reported'
            )
            ->addColumn(
                ReviewDetailsTableInterface::COLUMN_NAME_CUSTOMER_VERIFIED,
                Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Is customer verified'
            )
            ->addIndex(
                $setup->getIdxName(ReviewDetailsTableInterface::TABLE_NAME, [ReviewDetailsTableInterface::COLUMN_NAME_REVIEW_ID]),
                [ReviewDetailsTableInterface::COLUMN_NAME_REVIEW_ID]
            )
            ->setComment(
                'Review extended detail information'
            )->addForeignKey(
                $setup->getFkName(
                    $setup->getTable(ReviewDetailsTableInterface::TABLE_NAME),
                    ReviewDetailsTableInterface::COLUMN_NAME_REVIEW_ID,
                    $setup->getTable(ReviewInterface::TABLE_NAME),
                    ReviewInterface::COLUMN_NAME_REVIEW_ID
                ),
                ReviewDetailsTableInterface::COLUMN_NAME_REVIEW_ID,
                $setup->getTable(ReviewInterface::TABLE_NAME),
                ReviewInterface::COLUMN_NAME_REVIEW_ID,
                AdapterInterface::FK_ACTION_CASCADE
            );

        $setup->getConnection()->createTable($table);
    }
}
