<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleUniversalAnalytics\Block\Adminhtml\Order;

/**
 * Class Details
 * @package Scommerce\GoogleUniversalAnalytics\Block\Adminhtml\Order
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Scommerce\GoogleUniversalAnalytics\Helper\Data
     */
    protected $_helper;
	
	/**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_coreSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper,
        array $data = []
    ) {

        $this->_helper = $helper;
		$this->_coreSession = $context->getSession();
        parent::__construct($context, $data);
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
     * Render block html if google universal analytics conversion is active
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_helper->isEnabled() ? parent::_toHtml() : '';
    }

    /**
     * Retrieve domain url without www or subdomain
     *
     * @return string
     */
    public function getMainDomain()
    {
        if (!$this->hasData('main_domain')) {
            $host = $this->_request->getHttpHost();
            if (substr_count($host,'.')>1 && (!$this->getHelper()->isDomainAuto())){
                $this->setMainDomain(substr($host,strpos($host,'.')+1));
            }
            else{
                $this->setMainDomain('auto');
            }
        }
        return $this->getData('main_domain');
    }
	
	/**
     * Return refund data
     *
     * @return json
     */
    public function getRefundData()
    {
        return $this->_coreSession->getRefundOrder();
    }
	
	/**
     * Unset refund data
     *
     * @return json
     */
    public function unsRefundData()
    {
        return $this->_coreSession->unsRefundOrder();
    }
	
}
