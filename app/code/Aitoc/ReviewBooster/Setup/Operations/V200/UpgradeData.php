<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V200;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeDataOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V200\UpgradeData\CreateCampaignsWithStepsAndReminders as TransferRemindersOperation;
use Aitoc\ReviewBooster\Setup\Operations\V200\UpgradeData\TransferConfigs as TransferConfigsOperation;
use Aitoc\ReviewBooster\Setup\Operations\V200\UpgradeData\TransferUnsubscribedCustomers;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 */
class UpgradeData implements UpgradeDataOperationInterface
{
    /**
     * @var TransferRemindersOperation
     */
    private $transferRemindersOperation;

    /**
     * @var TransferUnsubscribedCustomers
     */
    private $transferUnsubscribedCustomersOperation;

    /**
     * @var TransferConfigsOperation
     */
    private $transferConfigsOperation;

    /**
     * UpgradeData constructor.
     * @param TransferRemindersOperation $transferRemindersOperation
     * @param TransferUnsubscribedCustomers $transferUnsubscribedCustomers
     * @param TransferConfigsOperation $transferConfigsOperation
     */
    public function __construct(
        TransferRemindersOperation $transferRemindersOperation,
        TransferUnsubscribedCustomers $transferUnsubscribedCustomers,
        TransferConfigsOperation $transferConfigsOperation
    ) {
        $this->transferRemindersOperation = $transferRemindersOperation;
        $this->transferUnsubscribedCustomersOperation = $transferUnsubscribedCustomers;
        $this->transferConfigsOperation = $transferConfigsOperation;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->transferReminders($setup);
        $this->transferUnsubscribedCustomers($setup);
        $this->transferConfigs($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function transferReminders(ModuleDataSetupInterface $setup)
    {
        $this->transferRemindersOperation->execute($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function transferUnsubscribedCustomers(ModuleDataSetupInterface $setup)
    {
        $this->transferUnsubscribedCustomersOperation->execute($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    private function transferConfigs(ModuleDataSetupInterface $setup)
    {
        $this->transferConfigsOperation->execute($setup);
    }
}
