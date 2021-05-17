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

use Aitoc\FollowUpEmails\Setup\Operations\V102\UpgradeSchema as UpgradeSchemaV102Operation;
use Aitoc\FollowUpEmails\Setup\Operations\AddGoogleParamsAndSalesRulesOperation;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade the ReviewBooster module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var UpgradeSchemaV102Operation
     */
    private $upgradeSchemaV102Operation;

    /**
     * @var AddGoogleParamsAndSalesRulesOperation
     */
    private $addGoogleParamsAndSalesRulesOperation;

    public function __construct(
        UpgradeSchemaV102Operation $upgradeSchemaV102Operation,
        AddGoogleParamsAndSalesRulesOperation $addGoogleParamsAndSalesRulesOperation
    ) {
        $this->upgradeSchemaV102Operation = $upgradeSchemaV102Operation;
        $this->addGoogleParamsAndSalesRulesOperation = $addGoogleParamsAndSalesRulesOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $version = $context->getVersion();

        $setup->startSetup();

        if (version_compare($version, '1.0.2', '<')) {
            $this->upgradeSchemaV102($setup);
        }

        if (version_compare($version, '1.0.3', '<')) {
            $this->addGoogleParamsAndSalesRules($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function upgradeSchemaV102(SchemaSetupInterface $setup)
    {
        $this->upgradeSchemaV102Operation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addGoogleParamsAndSalesRules(SchemaSetupInterface $setup)
    {
        $this->addGoogleParamsAndSalesRulesOperation->execute($setup);
    }
}
