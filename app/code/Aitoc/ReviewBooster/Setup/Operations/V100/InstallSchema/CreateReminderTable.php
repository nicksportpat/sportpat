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
use Aitoc\ReviewBooster\Api\Setup\V100\ReminderTableInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class CreateReminderTable
 */
class CreateReminderTable implements InstallSchemaOperationInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ReminderTableInterface::TABLE_NAME))
            ->addColumn(
                ReminderTableInterface::COLUMN_NAME_ID,
                Table::TYPE_BIGINT,
                20,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Reminder id'
            )
            ->addColumn(
                ReminderTableInterface::COLUMN_NAME_STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Store id'
            )
            ->addColumn(
                ReminderTableInterface::COLUMN_NAME_CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Customer Id'
            )
            ->addColumn(
                ReminderTableInterface::COLUMN_NAME_ORDER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Order id'
            )
            ->addColumn(
                ReminderTableInterface::COLUMN_NAME_STATUS,
                Table::TYPE_TEXT,
                32,
                ['unsigned' => false, 'nullable' => false, 'default' => 'pending'],
                'Status'
            )
            ->addColumn(
                ReminderTableInterface::COLUMN_NAME_CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created at'
            )->addColumn(
                ReminderTableInterface::COLUMN_NAME_SENT_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Sent at'
            )
            ->addColumn(
                ReminderTableInterface::COLUMN_NAME_CUSTOMER_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer name'
            )
            ->addColumn(
                ReminderTableInterface::COLUMN_NAME_CUSTOMER_EMAIL,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer email'
            )
            ->addColumn(
                ReminderTableInterface::COLUMN_NAME_PRODUCTS,
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => false],
                'Products'
            )
            ->addIndex(
                $setup->getIdxName(ReminderTableInterface::TABLE_NAME, [ReminderTableInterface::COLUMN_NAME_ORDER_ID]),
                [ReminderTableInterface::COLUMN_NAME_ORDER_ID]
            )
            ->setComment(
                'Review reminders'
            );

        $setup->getConnection()->createTable($table);
    }
}
