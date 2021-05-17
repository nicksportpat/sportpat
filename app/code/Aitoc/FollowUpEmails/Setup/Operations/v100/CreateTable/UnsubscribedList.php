<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Setup\Operations\v100\CreateTable;

use Aitoc\FollowUpEmails\Api\Setup\V100\UnsubscribedEmailAddressTableInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class UnsubscribedList
 */
class UnsubscribedList
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $unsubscribedListTable = $setup->getConnection()->newTable(
            $setup->getTable(UnsubscribedEmailAddressTableInterface::TABLE_NAME)
        )->addColumn(
            UnsubscribedEmailAddressTableInterface::COLUMN_NAME_ENTITY_ID,
            Table::TYPE_BIGINT,
            20,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Unsubscribe id'
        )->addColumn(
            UnsubscribedEmailAddressTableInterface::COLUMN_NAME_CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created at'
        )->addColumn(
            UnsubscribedEmailAddressTableInterface::COLUMN_NAME_EVENT_CODE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Event Code'
        )->addColumn(
            UnsubscribedEmailAddressTableInterface::COLUMN_NAME_EMAIL_ID,
            Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => true],
            'Email id'
        )->addColumn(
            UnsubscribedEmailAddressTableInterface::COLUMN_NAME_CUSTOMER_EMAIL,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true, 'default' => null],
            'Customer email'
        )->setComment(
            'Unsubscribed List'
        );

        $setup->getConnection()->createTable($unsubscribedListTable);
    }
}
