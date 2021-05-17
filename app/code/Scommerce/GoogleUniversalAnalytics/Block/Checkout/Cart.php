<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Scommerce\GoogleUniversalAnalytics\Block\Checkout;

/**
 * Cart Page Block
 */
class Cart extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Scommerce\GoogleUniversalAnalytics\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper,
        array $data = []
    ) {
        $this->_layout = $context->getLayout();
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get crosssell items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_layout->getBlockSingleton('Magento\Checkout\Block\Cart\Crosssell')->getItems();
    }

    /**
     * Return helper object
     *
     * @return \Scommerce\GoogleUniversalAnalytics\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * Render block html if Google Universal Analytics  module is active
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_helper->isEnabled() ? parent::_toHtml() : '';
    }
}