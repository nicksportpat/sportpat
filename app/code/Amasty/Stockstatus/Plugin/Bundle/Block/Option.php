<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Plugin\Bundle\Block;

class Option
{
    /**
     * @var \Amasty\Stockstatus\Helper\Data
     */
    protected $_helper;
    
    public function __construct(
        \Amasty\Stockstatus\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }


    public function aroundGetSelectionTitlePrice(
        \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $subject,
        \Closure $proceed,
        $selection,
        $includeContainer = true
    )
    {
        $result = $proceed($selection, $includeContainer);

        $status = $this->_helper->getCustomStockStatusText($selection);
        if ($status) {
            $find = "</span>";
            $replace = ' (' .$status . ')' . $find;
            $result = preg_replace("@$find@",  $replace, $result, 1);
        }
        return $result;
    }
}

