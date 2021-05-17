<?php
namespace Sportpat\HomeBanner\Model\Banner\Source;

use Magento\Framework\Option\ArrayInterface;

class BannerSize implements ArrayInterface
{
    const SQUARESMALL = 1;
    const RECTANGULARSMALL = 2;
    const RECTANGULARLARGE = 3;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::SQUARESMALL,
                'label' => __('square-small')
            ],
            [
                'value' => self::RECTANGULARSMALL,
                'label' => __('rectangular-small')
            ],
            [
                'value' => self::RECTANGULARLARGE,
                'label' => __('rectangular-large')
            ],
        ];
        return $options;
    }
}
