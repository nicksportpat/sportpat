<?php
namespace Sportpat\OrderSync\Model\Syncedorder\Source;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    const PENDING = 1;
    const SYNCED = 2;
    const ERROR = 3;
    const PROCESSING = 4;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::PENDING,
                'label' => __('PENDING')
            ],
            [
                'value' => self::SYNCED,
                'label' => __('SYNCED')
            ],
            [
                'value' => self::ERROR,
                'label' => __('ERROR')
            ],
            [
                'value' => self::PROCESSING,
                'label' => __('PROCESSING')
            ]
        ];
        return $options;
    }
}
