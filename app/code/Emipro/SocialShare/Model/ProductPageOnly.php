<?php
namespace Emipro\SocialShare\Model;

use Magento\Framework\Option\ArrayInterface;

class ProductPageOnly implements ArrayInterface
{

/*
 * Option getter
 * @return array
 */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value,
            ];
        }
        return $ret;
    }

/*
 * Get options in "key-value" format
 * @return array
 */
    public function toArray()
    {
        $choose = [
            0 => 'All Pages',
            1 => 'Product Page Only',
        ];
        return $choose;
    }
}
