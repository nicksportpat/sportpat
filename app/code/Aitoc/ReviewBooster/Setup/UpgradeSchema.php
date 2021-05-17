<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Setup;

use Aitoc\ReviewBooster\Setup\Operations\V103\UpgradeSchema as UpgradeSchemaV103Operation;
use Aitoc\ReviewBooster\Setup\Operations\V104\UpgradeSchema as UpgradeSchemaV104Operation;
use Aitoc\ReviewBooster\Setup\Operations\V105\UpgradeSchema as UpgradeSchemaV105Operation;
use Aitoc\ReviewBooster\Setup\Operations\V111\UpgradeSchema as UpgradeSchemaV111Operation;
use Aitoc\ReviewBooster\Setup\Operations\V121\UpgradeSchema as UpgradeSchemaV121Operation;
use Aitoc\ReviewBooster\Setup\Operations\V202\UpgradeSchema as UpgradeSchemaV202Operation;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Zend_Db_Exception;

/**
 * Upgrade the ReviewBooster module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var UpgradeSchemaV105Operation
     */
    private $upgradeSchemaV105Operation;

    /**
     * @var UpgradeSchemaV103Operation
     */
    private $upgradeSchemaV103Operation;

    /**
     * @var UpgradeSchemaV104Operation
     */
    private $upgradeSchemaV104Operation;

    /**
     * @var UpgradeSchemaV111Operation
     */
    private $upgradeSchemaV111Operation;

    /**
     * @var UpgradeSchemaV121Operation
     */
    private $upgradeSchemaV121Operation;

    /**
     * @var UpgradeSchemaV202Operation
     */
    private $upgradeSchemaV202Operation;

    /**
     * UpgradeSchema constructor.
     * @param UpgradeSchemaV103Operation $upgradeSchemaV103Operation
     * @param UpgradeSchemaV104Operation $upgradeSchemaV104Operation
     * @param UpgradeSchemaV105Operation $upgradeSchemaV105Operation
     * @param UpgradeSchemaV111Operation $upgradeSchemaV111Operation
     * @param UpgradeSchemaV121Operation $upgradeSchemaV121Operation
     * @param UpgradeSchemaV202Operation $upgradeSchemaV202Operation
     */
    public function __construct(
        UpgradeSchemaV103Operation $upgradeSchemaV103Operation,
        UpgradeSchemaV104Operation $upgradeSchemaV104Operation,
        UpgradeSchemaV105Operation $upgradeSchemaV105Operation,
        UpgradeSchemaV111Operation $upgradeSchemaV111Operation,
        UpgradeSchemaV121Operation $upgradeSchemaV121Operation,
        UpgradeSchemaV202Operation $upgradeSchemaV202Operation
    ) {
        $this->upgradeSchemaV103Operation = $upgradeSchemaV103Operation;
        $this->upgradeSchemaV104Operation = $upgradeSchemaV104Operation;
        $this->upgradeSchemaV105Operation = $upgradeSchemaV105Operation;
        $this->upgradeSchemaV111Operation = $upgradeSchemaV111Operation;
        $this->upgradeSchemaV121Operation = $upgradeSchemaV121Operation;
        $this->upgradeSchemaV202Operation = $upgradeSchemaV202Operation;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->upgradeSchemaV103($setup);
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->upgradeSchemaV104($setup);
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->upgradeSchemaV105($setup);
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $this->upgradeSchemaV111($setup);
        }

        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $this->upgradeSchemaV121($setup);
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $this->upgradeSchemaV202($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function upgradeSchemaV103(SchemaSetupInterface $setup)
    {
        $this->upgradeSchemaV103Operation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function upgradeSchemaV104(SchemaSetupInterface $setup)
    {
        $this->upgradeSchemaV104Operation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function upgradeSchemaV105(SchemaSetupInterface $setup)
    {
        $this->upgradeSchemaV105Operation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function upgradeSchemaV111(SchemaSetupInterface $setup)
    {
        $this->upgradeSchemaV111Operation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function upgradeSchemaV121(SchemaSetupInterface $setup)
    {
        $this->upgradeSchemaV121Operation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function upgradeSchemaV202(SchemaSetupInterface $setup)
    {
        $this->upgradeSchemaV202Operation->execute($setup);
    }
}
