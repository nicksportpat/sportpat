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
use Aitoc\FollowUpEmails\Api\Setup\V100\CampaignStepTableInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class CampaignSteps
 */
class CampaignSteps
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $campaignStepsTable = $setup->getConnection()->newTable(
            $setup->getTable(CampaignStepTableInterface::TABLE_NAME)
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_ENTITY_ID,
            Table::TYPE_BIGINT,
            20,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity id'
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_NAME,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true, 'default' => null],
            'Campaign step name'
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_TEMPLATE_ID,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Template id'
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_CAMPAIGN_ID,
            Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Campaign id'
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_DELAY_PERIOD,
            Table::TYPE_BIGINT,
            20,
            ['unsigned' => false, 'nullable' => false, 'default' => 1],
            'Delay Period'
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_STATUS,
            Table::TYPE_INTEGER,
            20,
            ['unsigned' => false, 'nullable' => false, 'default' => 1],
            'Status'
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_DELAY_UNIT,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false, 'default' => 'hours'],
            'Unit'
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_DISCOUNT_STATUS,
            Table::TYPE_INTEGER,
            20,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Discount Status'
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_DISCOUNT_PERCENT,
            Table::TYPE_BIGINT,
            20,
            ['unsigned' => false, 'nullable' => true],
            'Discount percent'
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_DISCOUNT_PERIOD,
            Table::TYPE_BIGINT,
            20,
            ['unsigned' => false, 'nullable' => true],
            'Discount period'
        )->addColumn(
            CampaignStepTableInterface::COLUMN_NAME_OPTIONS,
            Table::TYPE_TEXT,
            NULL,
            ['nullable' => false, 'default' => ''],
            'Options'
        )->addForeignKey(
            $setup->getFkName(
                CampaignStepTableInterface::TABLE_NAME,
                CampaignStepTableInterface::COLUMN_NAME_CAMPAIGN_ID,
                CampaignTableInterface::TABLE_NAME,
                CampaignTableInterface::COLUMN_NAME_ENTITY_ID
            ),
            CampaignStepTableInterface::COLUMN_NAME_CAMPAIGN_ID,
            $setup->getTable(CampaignTableInterface::TABLE_NAME),
            CampaignTableInterface::COLUMN_NAME_ENTITY_ID,
            Table::ACTION_CASCADE
        )->setComment(
            'Campaign Steps'
        );

        $setup->getConnection()->createTable($campaignStepsTable);
    }
}
