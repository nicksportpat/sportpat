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

use Aitoc\FollowUpEmails\Api\Setup\V100\CampaignTableInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class Campaigns
 */
class Campaigns
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $campaignsTable = $setup->getConnection()->newTable(
            $setup->getTable(CampaignTableInterface::TABLE_NAME)
        )->addColumn(
            CampaignTableInterface::COLUMN_NAME_ENTITY_ID,
            Table::TYPE_BIGINT,
            20,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Campaign id'
        )->addColumn(
            CampaignTableInterface::COLUMN_NAME_NAME,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true, 'default' => null],
            'Campaign name'
        )->addColumn(
            CampaignTableInterface::COLUMN_NAME_DESCRIPTION,
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'Campaign description'
        )->addColumn(
            CampaignTableInterface::COLUMN_NAME_EVENT_CODE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Event Code'
        )->addColumn(
            CampaignTableInterface::COLUMN_NAME_SENDER,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false, 'default' => 'general'],
            'Sender'
        )->addColumn(
            CampaignTableInterface::COLUMN_NAME_CUSTOMER_GROUP_IDS,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true, 'default' => null],
            'Customer group ids'
        )->addColumn(
            CampaignTableInterface::COLUMN_NAME_STORE_IDS,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Store ids'
        )->addColumn(
            CampaignTableInterface::COLUMN_NAME_STATUS,
            Table::TYPE_INTEGER,
            20,
            ['unsigned' => false, 'nullable' => false, 'default' => 1],
            'Status'
        )->setComment(
            'Campaigns'
        );

        $setup->getConnection()->createTable($campaignsTable);
    }
}
