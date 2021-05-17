<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V102;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeDataOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V102\UpgradeData\AddCustomerAttributeIsReviewBoosterSubscriber as AddCustomerAttributeIsReviewBoosterSubscriberOperation;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 */
class UpgradeData implements UpgradeDataOperationInterface
{
    /**
     * @var AddCustomerAttributeIsReviewBoosterSubscriberOperation
     */
    private $addCustomerAttributeIsReviewBoosterSubscriberOperation;

    /**
     * UpgradeData constructor.
     * @param AddCustomerAttributeIsReviewBoosterSubscriberOperation $addCustomerAttributeIsReviewBoosterSubscriberOperation
     */
    public function __construct(AddCustomerAttributeIsReviewBoosterSubscriberOperation $addCustomerAttributeIsReviewBoosterSubscriberOperation)
    {
        $this->addCustomerAttributeIsReviewBoosterSubscriberOperation = $addCustomerAttributeIsReviewBoosterSubscriberOperation;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->addCustomerAttributeIsReviewBoosterSubscriber($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    private function addCustomerAttributeIsReviewBoosterSubscriber(ModuleDataSetupInterface $setup)
    {
        $this->addCustomerAttributeIsReviewBoosterSubscriberOperation->execute($setup);
    }
}
