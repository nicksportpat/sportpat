<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\GiftCard\Model\Product;

/**
 * Class ReadHandler
 */
class ReadHandler extends \Amasty\Label\Model\Di\Wrapper
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        $name = ''
    ) {
        parent::__construct($objectManagerInterface, \Magento\GiftCard\Model\Product\ReadHandler::class);
    }
}
