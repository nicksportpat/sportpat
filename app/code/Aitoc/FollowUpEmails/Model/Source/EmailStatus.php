<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Email\StatusInterface;

/**
 * Class EmailStatus
 */
class EmailStatus implements OptionSourceInterface
{
    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            StatusInterface::STATUS_PENDING => __('Pending'),
            StatusInterface::STATUS_SENT => __('Sent'),
            StatusInterface::STATUS_HOLD => __('Hold'),
            StatusInterface::STATUS_ERROR => __('Error')
        ];
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach (self::getOptionArray() as $index => $value) {
            $options[] = [
                'value' => $index,
                'label' => $value
            ];
        }
        return $options;
    }
}
