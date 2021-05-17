<?php

/**
 * K3Live Module for ShopperApproved
 *
 * @package   ShopperApproved
 * @author    K3Live <support@k3live.com>
 * @copyright 2018 Copyright (c) Woopra (http://www.k3live.com/)
 * @license   Open Software License (OSL 3.0)
 */

namespace K3Live\ShopperApproved\Model\Config\Source;

class PopupInlineSelect implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            '0' => 'Pop-up',
            '1' => 'Inline'
        ];
    }
}
