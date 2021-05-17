<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Position
 * @package Amasty\Label\Model\Source
 */
class Position implements ArrayInterface
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
                'label' => __('Top Left')
            ],
            [
                'value' => 1,
                'label' => __('Top Center')
            ],
            [
                'value' => 2,
                'label' => __('Top Right')
            ],
            [
                'value' => 3,
                'label' => __('Middle Left')
            ],
            [
                'value' => 4,
                'label' => __('Middle Center')
            ],
            [
                'value' => 5,
                'label' => __('Middle Right')
            ],
            [
                'value' => 6,
                'label' => __('Bottom Left')
            ],
            [
                'value' => 7,
                'label' => __('Bottom Center')
            ],
            [
                'value' => 8,
                'label' => __('Bottom Right')
            ]
        ];
    }
}
