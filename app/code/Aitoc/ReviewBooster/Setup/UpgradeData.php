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

use Aitoc\FollowUpEmails\Setup\Base\UpgradeData as BaseUpgradeData;
use Aitoc\ReviewBooster\Setup\Operations\V102\UpgradeData as UpgradeDataV102Operation;
use Aitoc\ReviewBooster\Setup\Operations\V200\UpgradeData as UpgradeDataV200Operation;
use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 */
class UpgradeData extends BaseUpgradeData
{
    /**
     * @var UpgradeDataV102Operation
     */
    private $upgradeDataV102Operation;

    /**
     * @var UpgradeDataV200Operation
     */
    private $upgradeDataV200Operation;

    /**
     * UpgradeData constructor.
     * @param UpgradeDataV102Operation $upgradeDataV102Operation
     * @param UpgradeDataV200Operation $upgradeDataV200Operation
     */
    public function __construct(
        UpgradeDataV102Operation $upgradeDataV102Operation,
        UpgradeDataV200Operation $upgradeDataV200Operation
    ) {
        $this->upgradeDataV102Operation = $upgradeDataV102Operation;
        $this->upgradeDataV200Operation = $upgradeDataV200Operation;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->beginTransaction($setup);

        try {
            $setup->startSetup();

            $version = $context->getVersion();

            if (version_compare($version, '1.0.2', '<')) {
                $this->upgradeDataV102($setup);
                $this->commitTransaction($setup);
                $this->beginTransaction($setup);
            }


            if (version_compare($version, '2.0.0', '<')) {
                $this->upgradeDataV200($setup);
            }

            $setup->endSetup();
            $this->commitTransaction($setup);
        } catch (Exception $e) {
            $this->rollBackTransaction($setup);
            throw $e;
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    private function upgradeDataV102(ModuleDataSetupInterface $setup)
    {
        $this->upgradeDataV102Operation->execute($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function upgradeDataV200(ModuleDataSetupInterface $setup)
    {
        $this->upgradeDataV200Operation->execute($setup);
    }
}
