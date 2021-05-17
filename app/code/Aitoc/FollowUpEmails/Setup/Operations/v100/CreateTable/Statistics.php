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

use Aitoc\FollowUpEmails\Api\Setup\V100\StatisticTableInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class Statistics
 */
class Statistics
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $createStatisticsTable = $setup->getConnection()->newTable(
            $setup->getTable(StatisticTableInterface::TABLE_NAME)
        )->addColumn(
            StatisticTableInterface::COLUMN_NAME_ENTITY_ID,
            Table::TYPE_BIGINT,
            20,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity id'
        )->addColumn(
            StatisticTableInterface::COLUMN_NAME_CAMPAIGN_ID,
            Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Campaign id'
        )->addColumn(
            StatisticTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID,
            Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Campaign step id'
        )->addColumn(
            StatisticTableInterface::COLUMN_NAME_KEY,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Key'
        )->addColumn(
            StatisticTableInterface::COLUMN_NAME_VALUE,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true, 'default' => null],
            'Value'
        )->addColumn(
            StatisticTableInterface::COLUMN_NAME_UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Updated at'
        )->addColumn(
            StatisticTableInterface::COLUMN_NAME_EVENT_CODE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Event Code'
        )->setComment(
            'Statistics'
        );

        $setup->getConnection()->createTable($createStatisticsTable);
    }
}
