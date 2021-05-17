<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V102\UpgradeData;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeDataOperationInterface;
use Aitoc\ReviewBooster\Api\Setup\V102\CustomerAttributeCodeInterface;
use Exception;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class AddCustomerAttributeIsReviewBoosterSubscriber
 */
class AddCustomerAttributeIsReviewBoosterSubscriber implements UpgradeDataOperationInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * AddCustomerAttributeIsReviewBoosterSubscriber constructor.
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     * @throws Exception
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->createCustomerSetup($setup);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            CustomerAttributeCodeInterface::IS_REVIEW_BOOSTER_SUBSCRIBER,
            [
                'type' => 'int',
                'label' => 'Can receive Product Reviews &amp; Ratings notification',
                'input' => 'boolean',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 1000,
                'position' => 1000,
                'system' => 0,
                'default' => 1,
            ]
        );

        $customerEavConfig = $customerSetup->getEavConfig();
        $defaultAttributeSetId = $this->getDefaultAttributeSetId($customerEavConfig);
        $defaultAttributeGroupId = $this->getDefaultGroupIdByAttributeSetId($defaultAttributeSetId);

        //add attribute to attribute set
        //q: why not with prev addAttribute() call?
        $isReviewBoosterSubscriberAttribute = $this->getIsReviewBoosterSubscriberAttribute($customerEavConfig);
        $isReviewBoosterSubscriberAttribute->addData([
                'attribute_set_id' => $defaultAttributeSetId,
                'attribute_group_id' => $defaultAttributeGroupId,
                'used_in_forms' => ['adminhtml_customer'],
            ]);

        $isReviewBoosterSubscriberAttribute->save();//todo: save by repository
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return CustomerSetup
     */
    private function createCustomerSetup(ModuleDataSetupInterface $setup)
    {
        return $this->customerSetupFactory->create(['setup' => $setup]);
    }

    /**
     * @param Config $customerEavConfig
     * @return null|string
     * @throws LocalizedException
     */
    private function getDefaultAttributeSetId(Config $customerEavConfig)
    {
        $customerEntityType = $customerEavConfig->getEntityType(Customer::ENTITY);

        return $customerEntityType->getDefaultAttributeSetId();
    }

    /**
     * @param $attributeSetId
     * @return int|null
     */
    private function getDefaultGroupIdByAttributeSetId($attributeSetId)
    {
        /** @var AttributeSet $attributeSet */
        $attributeSet = $this->createEavEntityAttributeSet();

        return $attributeSet->getDefaultGroupId($attributeSetId);
    }

    /**
     * @return mixed
     */
    private function createEavEntityAttributeSet()
    {
        return $this->attributeSetFactory->create();
    }

    /**
     * @param Config $customerEavConfig
     * @return AbstractAttribute
     * @throws LocalizedException
     */
    private function getIsReviewBoosterSubscriberAttribute(Config $customerEavConfig)
    {
        return $customerEavConfig->getAttribute(
            Customer::ENTITY,
            CustomerAttributeCodeInterface::IS_REVIEW_BOOSTER_SUBSCRIBER
        );
    }
}
