<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */


namespace Aitoc\FollowUpEmails\Ui\Component\Listing\Column;

class DiscountTypeOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    const ACTION_TYPE_FIXED_OPTION = 0;
    const ACTION_TYPE_PERCENT_OPTION = 1;
    const ACTION_TYPE_RULE_OPTION = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ACTION_TYPE_FIXED_OPTION,
                'label' => __('Fixed')
            ],
            [
                'value' => self::ACTION_TYPE_PERCENT_OPTION,
                'label' => __('Percentage')
            ],
            [
                'value' => self::ACTION_TYPE_RULE_OPTION,
                'label' => __('Use Sales Rule')
            ]
        ];
    }
}
