<?php

/**
 * K3Live Module for ShopperApproved
 *
 * @package   ShopperApproved
 * @author    K3Live <support@k3live.com>
 * @copyright 2018 Copyright (c) Woopra (http://www.k3live.com/)
 * @license   Open Software License (OSL 3.0)
 */

namespace K3Live\ShopperApproved\Block;

use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\View\Element\Template\Context;
use K3Live\ShopperApproved\Helper\Data;

class Script extends \Magento\Framework\View\Element\Template
{
    private $context;
    private $customerSession;
    private $dataHelper;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        Data $dataHelper,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }
    
    public function getSetting($key = null)
    {
        static $data;
        if (empty($data)) {
            $data = [
                'enabled' => $this->dataHelper->getEnabled(),
                'site_id' => $this->dataHelper->getSiteId(),
                'site_token' => $this->dataHelper->getSiteToken(),
                'api_token' => $this->dataHelper->getApiToken(),
                'popup_inline' => $this->dataHelper->getPopupInline(),
                'auto_populate' => $this->dataHelper->getAutoPopulate(),
                'send_all' => $this->dataHelper->getSendAll(),
                'mandatory_comments' => $this->dataHelper->getMandatoryComments(),
                'cancel_review' => $this->dataHelper->getCancelReview()
            ];
        }
        $customer = $this->customerSession->getCustomer();
        if (!empty($customer)) {
            if ($customer->getName() != ' ') {
                $data['customer_name'] = $this->_escaper->escapeQuote($customer->getName());
            }
            $data['customer_email'] = $customer->getEmail();
            $address = $customer->getDefaultBillingAddress();
            if (!empty($address)) {
                $address = $customer->getDefaultShippingAddress();
                $data['customer_region'] = $this->_escaper->escapeQuote($address->getRegion());
                $data['customer_country'] = $address->getCountryId();
            }
        }
        if ($this->customerSession->getData('sa_checkout_success_order_id')) {
            $data['order_id'] = $this->customerSession->getData('sa_checkout_success_order_id');
        }
        if (isset($data[$key])) {
            return $data[$key];
        } else {
            return null;
        }
    }
}
