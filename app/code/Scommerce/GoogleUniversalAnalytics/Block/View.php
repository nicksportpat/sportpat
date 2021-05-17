<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Scommerce\GoogleUniversalAnalytics\Block;

/**
 * Catalog Product View Page Block
 */
class View extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Registry
     */

    protected $_registry;

    /**
     * @var \Scommerce\GoogleUniversalAnalytics\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $_coreSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Session\Generic $coreSession
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Session\Generic $coreSession,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_helper = $helper;
        $this->_product = $product;
        $this->_coreSession = $coreSession;
        parent::__construct($context, $data);
    }

    /**
     * Return catalog product object
     *
     * @return \Magento\Catalog\Model\Product
     */

    public function getProduct()
    {
        return $this->_registry->registry('product');
    }

    /**
     * @return \Magento\Framework\Session\Generic
     */
    public function getCoreCookie()
    {
        return $this->_coreSession;
    }

    /**
     * Return catalog product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProducts($_productIds)
    {
        return $this->getProduct()
            ->getCollection()
            ->addAttributeToSelect(array('name','sku'))
            ->addAttributeToFilter('entity_id',array('in' => $_productIds))
            ->addUrlRewrite();
    }

    /**
     * Return catalog current category object
     *
     * @return \Magento\Catalog\Model\Category
     */

    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
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
     * Render block html if google universal analytics is active
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_helper->isEnabled() ? parent::_toHtml() : '';
    }
}
