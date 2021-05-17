<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */


namespace Aitoc\FollowUpEmails\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use \Magento\SalesRule\Model\Data\Rule;

class SaleRuleOptions implements OptionSourceInterface
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    private $ruleCollectionFactory;

    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    public function toOptionArray()
    {
        $options = [
            ['label' => _('--Please Select--'), 'value' => '']
        ];

        $ruleCollection = $this->ruleCollectionFactory->create()
            ->addFieldToFilter(\Magento\SalesRule\Model\Data\Rule::KEY_COUPON_TYPE,
                ['neq' => \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON]);
        foreach ($ruleCollection as $rule) {
            $options[] = ['label' => $rule->getName(), 'value' => $rule->getId()];
        }

        return $options;
    }
}
