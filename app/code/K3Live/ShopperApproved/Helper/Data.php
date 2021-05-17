<?php

/**
 * K3Live Module for ShopperApproved
 *
 * @package   ShopperApproved
 * @author    K3Live <support@k3live.com>
 * @copyright 2018 Copyright (c) Woopra (http://www.k3live.com/)
 * @license   Open Software License (OSL 3.0)
 */

namespace K3Live\ShopperApproved\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ENABLED = 'k3live_shopperapproved/shopper_approved/enabled';
    const SITE_ID = 'k3live_shopperapproved/shopper_approved/site_id';
    const SITE_TOKEN = 'k3live_shopperapproved/shopper_approved/site_token';
    const API_TOKEN = 'k3live_shopperapproved/shopper_approved/api_token';
    const POPUP_INLINE = 'k3live_shopperapproved/shopper_approved_customize/popup_inline';
    const AUTO_POPULATE = 'k3live_shopperapproved/shopper_approved_customize/auto_populate';
    const SEND_ALL = 'k3live_shopperapproved/shopper_approved_customize/send_all';
    const MANDATORY_COMMENTS = 'k3live_shopperapproved/shopper_approved_customize/mandatory_comments';
    const CANCEL_REVIEW = 'k3live_shopperapproved/shopper_approved_customize/cancel_review';
    
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
    }
 
    public function getEnabled()
    {
        return $this->scopeConfig->getValue(self::ENABLED);
    }
    public function getSiteId()
    {
        return $this->scopeConfig->getValue(self::SITE_ID);
    }
    public function getSiteToken()
    {
        return $this->scopeConfig->getValue(self::SITE_TOKEN);
    }
    public function getApiToken()
    {
        return $this->scopeConfig->getValue(self::API_TOKEN);
    }
    public function getPopupInline()
    {
        return $this->scopeConfig->getValue(self::POPUP_INLINE);
    }
    public function getAutoPopulate()
    {
        return $this->scopeConfig->getValue(self::AUTO_POPULATE);
    }
    public function getSendAll()
    {
        return $this->scopeConfig->getValue(self::SEND_ALL);
    }
    public function getMandatoryComments()
    {
        return $this->scopeConfig->getValue(self::MANDATORY_COMMENTS);
    }
    public function getCancelReview()
    {
        return $this->scopeConfig->getValue(self::CANCEL_REVIEW);
    }
}
