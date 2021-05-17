<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Amasty\Label\Model\Source
 */
class Status implements ArrayInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('Inactive')
            ],
            [
                'value' => 1,
                'label' => __('Active')
            ]
        ];
    }
}
