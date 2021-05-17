<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Setup;

use Aitoc\FollowUpEmails\Setup\Operations\v100\CreateTable\Campaigns as CreateCampaignsTableOperation;
use Aitoc\FollowUpEmails\Setup\Operations\v100\CreateTable\CampaignSteps as CreateCampaignStepsTableOperation;
use Aitoc\FollowUpEmails\Setup\Operations\v100\CreateTable\EmailAttributes as CreateEmailAttributesTableOperation;
use Aitoc\FollowUpEmails\Setup\Operations\v100\CreateTable\Emails as CreateEmailsTableOperation;
use Aitoc\FollowUpEmails\Setup\Operations\v100\CreateTable\Statistics as CreateStatisticsTableOperation;
use Aitoc\FollowUpEmails\Setup\Operations\v100\CreateTable\UnsubscribedList as CreateUnsubscribedListTableOperation;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 *
 * @package Aitoc\FollowUpEmails\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var CreateCampaignsTableOperation
     */
    private $createCampaignsTableOperation;

    /**
     * @var CreateCampaignStepsTableOperation
     */
    private $createCampaignStepsTableOperation;

    /**
     * @var CreateEmailsTableOperation
     */
    private $createEmailsTableOperation;

    /**
     * @var CreateEmailAttributesTableOperation
     */
    private $createEmailAttributesTableOperation;

    /**
     * @var CreateUnsubscribedListTableOperation
     */
    private $createUnsubscribedListTableOperation;

    /**
     * @var CreateStatisticsTableOperation
     */
    private $createStatisticsTableOperation;

    /**
     * @param CreateCampaignsTableOperation $createCampaignsTableOperation
     * @param CreateCampaignStepsTableOperation $createCampaignStepsTableOperation
     * @param CreateEmailsTableOperation $createEmailsTableOperation
     * @param CreateEmailAttributesTableOperation $createEmailAttributesTableOperation
     * @param CreateUnsubscribedListTableOperation $createUnsubscribedListOperation
     * @param CreateStatisticsTableOperation $createStatisticsOperation
     */
    public function __construct(
        CreateCampaignsTableOperation $createCampaignsTableOperation,
        CreateCampaignStepsTableOperation $createCampaignStepsTableOperation,
        CreateEmailsTableOperation $createEmailsTableOperation,
        CreateEmailAttributesTableOperation $createEmailAttributesTableOperation,
        CreateUnsubscribedListTableOperation $createUnsubscribedListOperation,
        CreateStatisticsTableOperation $createStatisticsOperation
    ) {
        $this->createCampaignsTableOperation = $createCampaignsTableOperation;
        $this->createCampaignStepsTableOperation = $createCampaignStepsTableOperation;
        $this->createEmailsTableOperation = $createEmailsTableOperation;
        $this->createEmailAttributesTableOperation = $createEmailAttributesTableOperation;
        $this->createUnsubscribedListTableOperation = $createUnsubscribedListOperation;
        $this->createStatisticsTableOperation = $createStatisticsOperation;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createCampaignsTable($setup);
        $this->createCampaignStepsTable($setup);
        $this->createEmailsTable($setup);
        $this->createEmailAttributesTable($setup);
        $this->createUnsubscribedListTable($setup);
        $this->createStatisticsTable($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function createCampaignsTable(SchemaSetupInterface $setup)
    {
        $this->createCampaignsTableOperation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function createCampaignStepsTable(SchemaSetupInterface $setup)
    {
        $this->createCampaignStepsTableOperation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function createEmailsTable(SchemaSetupInterface $setup)
    {
        $this->createEmailsTableOperation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function createEmailAttributesTable(SchemaSetupInterface $setup)
    {
        $this->createEmailAttributesTableOperation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function createUnsubscribedListTable(SchemaSetupInterface $setup)
    {
        $this->createUnsubscribedListTableOperation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function createStatisticsTable(SchemaSetupInterface $setup)
    {
        $this->createStatisticsTableOperation->execute($setup);
    }
}
