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

use Aitoc\FollowUpEmails\Api\Setup\V100\CampaignStepTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\V100\EmailTableInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class Emails
 */
class Emails
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $emailsTable = $setup->getConnection()->newTable(
            $setup->getTable(EmailTableInterface::TABLE_NAME)
        )->addColumn(
            EmailTableInterface::COLUMN_NAME_ENTITY_ID,
            Table::TYPE_BIGINT,
            20,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Email id'
        )->addColumn(
            EmailTableInterface::COLUMN_NAME_CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created at'
        )->addColumn(
            EmailTableInterface::COLUMN_NAME_SCHEDULED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Scheduled at'
        )->addColumn(
            EmailTableInterface::COLUMN_NAME_SENT_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Sent at'
        )->addColumn(
            EmailTableInterface::COLUMN_NAME_OPENED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Opened at'
        )->addColumn(
            EmailTableInterface::COLUMN_NAME_TRANSITED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Transited at'
        )->addColumn(
            EmailTableInterface::COLUMN_NAME_CUSTOMER_EMAIL,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true, 'default' => null],
            'Customer email'
        )->addColumn(
            EmailTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID,
            Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Campaign Step id'
        )->addColumn(
            EmailTableInterface::COLUMN_NAME_STATUS,
            Table::TYPE_INTEGER,
            20,
            ['unsigned' => false, 'nullable' => false, 'default' => 1],
            'Status'
        )->addColumn(
            EmailTableInterface::COLUMN_NAME_SECRET_CODE,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true, 'default' => null],
            'Unsubscribe code'
        )->addForeignKey(
            $setup->getFkName(
                EmailTableInterface::TABLE_NAME,
                EmailTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID,
                CampaignStepTableInterface::TABLE_NAME,
                CampaignStepTableInterface::COLUMN_NAME_ENTITY_ID
            ),
            EmailTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID,
            $setup->getTable(CampaignStepTableInterface::TABLE_NAME),
            CampaignStepTableInterface::COLUMN_NAME_ENTITY_ID,
            Table::ACTION_CASCADE
        )->setComment(
            'Emails'
        );

        $setup->getConnection()->createTable($emailsTable);
    }
}
