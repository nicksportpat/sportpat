<?php
namespace Sportpat\Tabcontent\Model\Tabcontent\Source;

use Magento\Framework\Option\ArrayInterface;

class ForGender implements ArrayInterface
{
    const MEN = 1;
    const WOMEN = 2;
    const ADULT = 3;
    const YOUTH = 4;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::MEN,
                'label' => __('MEN')
            ],
            [
                'value' => self::WOMEN,
                'label' => __('WOMEN')
            ],
            [
                'value' => self::ADULT,
                'label' => __('ADULT')
            ],
            [
                'value' => self::YOUTH,
                'label' => __('YOUTH')
            ],
        ];
        return $options;
    }
}
