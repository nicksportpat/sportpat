<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */


namespace Aitoc\FollowUpEmails\Setup\Operations;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Aitoc\FollowUpEmails\Api\Setup\V100\CampaignStepTableInterface;

class AddGoogleParamsAndSalesRulesOperation implements UpgradeSchemaOperationInterface
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $dbAdapter = $setup->getConnection();
        $tableName = $setup->getTable(CampaignStepTableInterface::TABLE_NAME);

        if (!$dbAdapter->tableColumnExists($tableName, CampaignStepTableInterface::COLUMN_NAME_DISCOUNT_TYPE)) {
            $dbAdapter->addColumn(
                $tableName,
                CampaignStepTableInterface::COLUMN_NAME_DISCOUNT_TYPE,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Email Campaign Discount Type'
                ]
            );
        }

        if (!$dbAdapter->tableColumnExists($tableName, CampaignStepTableInterface::COLUMN_NAME_SALES_RULE)) {
            $dbAdapter->addColumn(
                $tableName,
                CampaignStepTableInterface::COLUMN_NAME_SALES_RULE,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => true,
                    'comment' => 'Email Campaign Sales Rule Id'
                ]
            );
        }

        if (!$dbAdapter->tableColumnExists($tableName, CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_SOURCE)) {
            $dbAdapter->addColumn(
                $tableName,
                CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_SOURCE,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Email Campaign Google Utm Source'
                ]
            );
        }

        if (!$dbAdapter->tableColumnExists($tableName, CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_MEDIUM)) {
            $dbAdapter->addColumn(
                $tableName,
                CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_MEDIUM,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Email Campaign Google Utm Medium'
                ]
            );
        }

        if (!$dbAdapter->tableColumnExists($tableName, CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_TERM)) {
            $dbAdapter->addColumn(
                $tableName,
                CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_TERM,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Email Campaign Google Utm Term'
                ]
            );
        }

        if (!$dbAdapter->tableColumnExists($tableName, CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_CONTENT)) {
            $dbAdapter->addColumn(
                $tableName,
                CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_CONTENT,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Email Campaign Google Utm Content'
                ]
            );
        }

        if (!$dbAdapter->tableColumnExists($tableName, CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_CAMPAIGN)) {
            $dbAdapter->addColumn(
                $tableName,
                CampaignStepTableInterface::COLUMN_NAME_GOOGLE_UTM_CAMPAIGN,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Email Campaign Google Utm Campaign'
                ]
            );
        }
    }
}
