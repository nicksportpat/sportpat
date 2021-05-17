<?php
namespace Sportpat\Tabcontent\Model\Tabcontent\Source;

use Magento\Framework\Option\ArrayInterface;

class TabContenttype implements ArrayInterface
{
    const SIZE_CHART = 1;
    const COMPARISON_CHART = 2;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::SIZE_CHART,
                'label' => __('size chart')
            ],
            [
                'value' => self::COMPARISON_CHART,
                'label' => __('comparison chart')
            ],
        ];
        return $options;
    }
}
