<?php
/**
 * Google Universal Analytics Data Helper
 *
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleUniversalAnalytics\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Admin configuration paths
     *
     */
    const XML_PATH_ENABLED 					= 'googleuniversalanalytics/general/active';
    const XML_PATH_LICENSE_KEY 				= 'googleuniversalanalytics/general/license_key';
    const XML_PATH_ACCOUNT_ID 				= 'googleuniversalanalytics/general/account_id';
    const XML_PATH_ANONYMIZE_IP 			= 'googleuniversalanalytics/general/anonymize_ip';
    const XML_PATH_DISPLAY_FEATURE 			= 'googleuniversalanalytics/general/display_feature';
    const XML_PATH_ENABLE_USERID 			= 'googleuniversalanalytics/general/enable_userid';
    const XML_PATH_DOMAIN_AUTO 				= 'googleuniversalanalytics/general/domain_auto';
    const XML_PATH_ECOMMERCE 				= 'googleuniversalanalytics/general/ecommerce_enabled';
    const XML_PATH_LINKER 					= 'googleuniversalanalytics/general/linker_enabled';
    const XML_PATH_DOMAINS_TO_LINK 			= 'googleuniversalanalytics/general/domains_to_link';
    const XML_PATH_LINK_ACCOUNTS_ENABLED 	= 'googleuniversalanalytics/general/link_accounts_enabled';
    const XML_PATH_LINKED_ACCOUNT_ID 		= 'googleuniversalanalytics/general/linked_account_id';
    const XML_PATH_LINKED_ACCOUNT_NAME 		= 'googleuniversalanalytics/general/linked_account_name';
    const XML_PATH_BASE 					= 'googleuniversalanalytics/general/base';
	const XML_PATH_AJAX_ENABLED             = 'googleuniversalanalytics/general/ajax_enabled';
    const XML_PATH_ENHANCED_ECOMMERCE 		= 'googleuniversalanalytics/enhanced/enhanced_ecommerce_enabled';
    const XML_PATH_ENHANCED_STEPS 			= 'googleuniversalanalytics/enhanced/steps';
    const XML_PATH_ENHANCED_BRAND_DROPDOWN  = 'googleuniversalanalytics/enhanced/brand_dropdown';
    const XML_PATH_ENHANCED_BRAND_TEXT      = 'googleuniversalanalytics/enhanced/brand_text';
    const XML_PATH_ENHANCED_VARIANT         = 'googleuniversalanalytics/enhanced/variant';
    const XML_PATH_SOOT         			= 'googleuniversalanalytics/enhanced/send_offline_order_transaction';
    const XML_PATH_STON         			= 'googleuniversalanalytics/enhanced/send_transaction_on_invoice';
    const XML_PATH_SPOT         			= 'googleuniversalanalytics/enhanced/send_phone_order_transaction';
    const XML_PATH_ENHANCED_SOURCE_TEXT     = 'googleuniversalanalytics/enhanced/admin_source';
    const XML_PATH_ENHANCED_MEDIUM_TEXT     = 'googleuniversalanalytics/enhanced/admin_medium';
	const XML_PATH_ENHANCED_SIOS			= 'googleuniversalanalytics/enhanced/send_impression_on_scroll';
	const XML_PATH_ENHANCED_PIC_TEXT		= 'googleuniversalanalytics/enhanced/product_item_class';
    const XML_PATH_ENHANCED_DEBUGGING       = 'googleuniversalanalytics/enhanced/debugging';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Scommerce\Core\Helper\Data
     */
    protected $_data;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var string
     */
    protected $_domainHost;

    /**
     * @var string
     */
    protected $_cid;

    /**
     * @var string
     */
    public $_utmz;

    /**
     * @var string
     */
    private $_cn;

    /**
     * @var string
     */
    private $_cs;

    /**
     * @var string
     */
    private $_cm;

    /**
     * @var string
     */
    private $_cc;

    /**
     * @var string
     */
    private $_ck;

    /**
     * @var string
     */
    private $_gclid;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;


    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Scommerce\Core\Helper\Data $data
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Escaper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry,
        \Scommerce\Core\Helper\Data $data,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->_data = $data;
        $this->_product = $product;
        $this->_objectManager = $objectManager;
        $this->_escaper = $escaper;
        $this->_domainHost = $this->getBaseURL();
        if (isset($_COOKIE['_ga'])){
            $this->_cid = $this->gaParseCookie($_COOKIE['_ga']);
        }
        if (isset($_COOKIE["utmz"])) {
            $this->_utmz = $this->_cid.".1.1.".str_replace('"','',$_COOKIE["utmz"]);
        }

        $this->_authSession = $authSession;

        //traffic source parameters
        $this->_cn ='';
        $this->_cs ='';
        $this->_cm ='';
        $this->_ck ='';
        $this->_cc ='';
        $this->_gclid ='';
    }

    /**
     * Retrieve store name
     *
     * @return string|null
     */
    public function getStoreName($storeId=null)
    {
        return (string)$this->scopeConfig->getValue(
            'general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId
        );
    }
	
	/**
     * Retrieve base url
     *
     * @return string|null
     */
    public function getBaseURL($storeId=null)
    {
        return (string)$this->scopeConfig->getValue(
            'web/unsecure/base_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId
        );
    }

    /**
     * returns whether module is enabled or not
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) && $this->isLicenseValid() && $this->getAccountId();
    }
    
    /**
     * returns account id
     * @param storeId
     * @return string
     */
    public function getAccountId($storeId=null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACCOUNT_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }

    /**
     * returns whether link account feature is enabled or not
     * @param storeId
     * @return boolean
     */
    public function isLinkAccountsEnabled($storeId=null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_LINK_ACCOUNTS_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId
        ) && strlen($this->getLinkedAccountId($storeId)) && strlen($this->getLinkedAccountName($storeId));
    }


    /**
     * returns linked account id
     * @param storeId
     * @return string
     */
    public function getLinkedAccountId($storeId=null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LINKED_ACCOUNT_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }

    /**
     * returns linked account name
     * @return string
     */
    public function getLinkedAccountName($storeId=null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LINKED_ACCOUNT_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }
	
	/**
     * returns whether ajax add to basket is enabled or not
     * @return boolean
     */
    public function isAjaxEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_AJAX_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * returns product category name
     * @param product \Magento\Catalog\Model\Product
     * @return string
     */
    public function getProductCategoryName($_product)
    {
        $_cats = $_product->getCategoryIds();
        $_categoryId = array_pop($_cats);

        $_cat = $this->_objectManager->create('\Magento\Catalog\Model\Category')->load($_categoryId);
		return $this->getParentsCategory($_cat);
    }
	
	/**
     * returns category path information 
     * @param $current Mage_Catalog_Model_Category
     * @return string
     */	
    public function getParentsCategory($current) 
	{
        $parentIds = explode("/", $current->getPath());
        array_shift($parentIds); // ROOT CATEGORY (ID = 1)
        array_shift($parentIds); // DEFAULT CATEGORY (ID = 2)   

        $names = array();
        foreach ($parentIds as &$value) {
            $category = $this->_objectManager->create('\Magento\Catalog\Model\Category')->load($value);
            $names[]= $category->getName();
        }           
           
        $cats_tree = join('/', $names);
        return $cats_tree;
    }

    /**
     * returns product category name
     * @param quoteItem \Magento\Quote\Model\QuoteItem
     * @return string
     */
    public function getQuoteCategoryName($quoteItem)
    {
		if ($_catName = $quoteItem->getGoogleCategory()){
			return $_catName;
        }
		
        $_product = $this->_product->load($quoteItem->getProductId());

        return $this->getProductCategoryName($_product);
    }

    /**
     * returns product brand name
     * @param quoteItem \Magento\Quote\Model\QuoteItem
     * @return string
     */
    public function getQuoteBrand($quoteItem)
    {
        $_product = $this->_product->load($quoteItem->getProductId());

        return $this->getBrand($_product);
    }

    /**
     * returns Anonymize IP is on or off
     * @return boolean
     */
    public function isAnonymizeIp()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ANONYMIZE_IP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * returns display feature is on or off
     *
     * @return boolean
     */
    public function isDisplayFeature()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DISPLAY_FEATURE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * returns user id feature is on or off
     *
     * @return boolean
     */
    public function isUserIdEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_USERID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * returns whether domain auto is enabled or not
     *
     * @return boolean
     */
    public function isDomainAuto()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DOMAIN_AUTO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * returns whether ecommerce enabled or not
     *
     * @return boolean
     */
    public function isEcommerceEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ECOMMERCE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * returns whether linker is enabled or not
     *
     * @return boolean
     */
    public function isLinkerEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_LINKER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * returns domains to link string
     * @return string
     */
    public function getDomainsToLink()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DOMAINS_TO_LINK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * returns whether enhanced ecommerce is enabled or not
     * @return boolean
     */
    public function isEnhancedEcommerceEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENHANCED_ECOMMERCE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	/**
     * returns whether send impression on scroll is enabled or not
     * @return boolean
     */
    public function isSIOSEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENHANCED_SIOS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	 /**
     * returns product item class static text
     * @return string
     */
    public function getPICText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENHANCED_PIC_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * returns whether debugging on or not
     * @return boolean
     */
    public function getDebugging()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENHANCED_DEBUGGING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * returns whether transaction data should go to GA on order creation or not
     * @param storeId
     * @return boolean
     */
    public function sendTransactionDataOffline($storeId=null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SOOT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }

    /**
     * returns whether transaction data should go to GA on admin order creation or not
     * @param storeId
     * @return boolean
     */
    public function sendPhoneOrderTransaction($storeId=null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SPOT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }

    /**
     * returns source static text
     * @param int $storeId Store view ID
     * @return string
     */
    public function getSourceText($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENHANCED_SOURCE_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId
        );
    }

    /**
     * returns medium static text
     * @param int $storeId Store view ID
     * @return string
     */
    public function getMediumText($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENHANCED_MEDIUM_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId
        );
    }


    /**
     * returns whether transaction data should go to GA on invoice creation or not
     * @param storeId
     * @return boolean
     */
    public function sendTransactionDataOnInvoice($storeId=null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_STON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }

    /**
     * returns checkout steps which needs to be tracked
     * @return array
     */
    public function getSteps()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENHANCED_STEPS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * returns whether base order data is enabled or not
     * @return boolean
     */
    public function sendBaseData($storeId=null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_BASE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }

    /**
     * returns attribute id of brand
     * @return string
     */
    public function getBrandDropdown()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENHANCED_BRAND_DROPDOWN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * returns brand static text
     * @return string
     */
    public function getBrandText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENHANCED_BRAND_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * returns brand value using product or text
     * @param $product /Magento/Catalog/Model/Product
     * @return int
     */
    public function getBrand($product)
    {
		$_product = $this->_product->load($product->getId());
        if ($attribute = $this->getBrandDropdown()){
            $data = $_product->getAttributeText($attribute);
			if (is_array($data)) $data = end($data);
            if (strlen($data)==0){
                $data = $_product->getData($attribute);
            }
            return $data;
        }
        return $this->getBrandText();
    }

    /**
     * retrieve list of checkout steps
     *
     * @return array
     */
    public function getStepsArray()
    {
        $steps = $this->getSteps();

        if (!$steps) {
            return array();
        }

        return explode(',', $steps);
    }

    /**
     * check if checkout step exists in configuration
     *
     * @return bool
     */
    public function stepExists($step)
    {
        return in_array($step, $this->getStepsArray());
    }

    /**
     * retrieve step number
     *
     * @return int
     */
    public function getStepNumber($step)
    {
        return array_search($step, $this->getStepsArray()) + 1;
    }


    /**
     * Retrieve domain url without www or subdomain
     *
     * @return string
     */
    public function getDomain()
    {
        $host = $this->_request->getHttpHost();
		if (substr($host,0,1)=="."){
			return $host;
		}
		else{
			return '.'.$host;
		}
    }

    /**
     * Retrieving cid for GA from __ga cookie
     *
     * @return string
     */
    public function gaParseCookie($google_cookie) {
        if (isset($google_cookie)) {
            list($version,$domainDepth, $cid1, $cid2) = preg_split('[\.]', $google_cookie,4);
            $contents = array('version' => $version, 'domainDepth' => $domainDepth, 'cid' => $cid1.'.'.$cid2);
            $cid = $contents['cid'];
        }
        else $cid = $this->gen_uuid();
        return $cid;
    }

    /**
     * Generating unique id for GA if __ga cookie doesn't exist
     *
     * @return string
     */
    protected function gen_uuid()
    {
        // Generates a UUID. A UUID is required for the measurement protocol.
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    /**
     * Adding product data of the current cart to send to GA as part of measurement protocol call
     *
     * @return array
     */
    public function addProductData($order,$data,$bInvoice=false)
    {
        $intCtr = 1;
        $products = array();

        foreach ($order->getAllItems() as $item){
            if($item->getBasePrice()<=0) continue;
            $products = array(
                'pr'.$intCtr.'nm'	=> $this->_escaper->escapeJsQuote($item->getName(), '"'), // Item name. Required.
                'pr'.$intCtr.'pr' 	=> $this->sendBaseData()==true ? $item->getBasePrice() : $item->getPrice(), // Item price.
                'pr'.$intCtr.'qt' 	=> $bInvoice==true ? $item->getQty() : $item->getQtyOrdered(), // Item quantity.
                'pr'.$intCtr.'id' 	=> $item->getSku(), // Item code / SKU.
                'pr'.$intCtr.'ca' 	=> $this->_escaper->escapeJsQuote($this->getQuoteCategoryName($item), '"'), // Item category.
                'pr'.$intCtr.'br' 	=> $this->_escaper->escapeJsQuote($this->getQuoteBrand($item), '"'), // Brand
                'pr'.$intCtr.'ps'   => $intCtr // Item Position.
            );
            $data = array_merge($data,$products);

            $intCtr++;
        }

        return $data;
    }

    /**
     * Build order data to send to Google Analytics
     *
     * @return array
     */
    public function addOrderData($order,$storeId,$accountId)
    {
        $orderId = $order->getIncrementId();

        $this->_cid = $this->gaParseCookie($order->getGoogleCookie());

        $this->gaParseTSCookie($order->getGoogleTsCookie(), $storeId);

        if ($this->sendBaseData($storeId)):
            $orderCurrency 		= $order->getBaseCurrencyCode();
            $orderGrandTotal 	= $order->getBaseGrandTotal();
            $orderShippingTotal	= $order->getBaseShippingAmount();
            $orderTax			= $order->getBaseTaxAmount();
        else:
            $orderCurrency 		= $order->getOrderCurrencyCode();
            $orderGrandTotal 	= $order->getGrandTotal();
            $orderShippingTotal	= $order->getShippingAmount();
            $orderTax			= $order->getTaxAmount();
        endif;

        /* Sending Transactional Data to GA*/
        $data = array(
            'v' 	=> 1, // The version of the measurement protocol
            'tid' 	=> $accountId, // Google Analytics account ID (UA-98765432-1)
            'cid' 	=> $this->_cid, // The UUID
            't'     => 'pageview', // Hit Type
            'dh'    => $this->_domainHost, // Domain Hostname
            'dp'    => '/checkout/onepage/success', // Page
            'dt'    => 'Order Confirmation',// Page Title
            'ti'	=> $orderId,       // Transaction ID. Required.
            'cu'	=> $orderCurrency,  // Transaction currency code.
            'ta'	=> $this->getStoreName(),  // Transaction affiliation.
            'tr'	=> number_format($orderGrandTotal,2),        // Transaction revenue.
            'ts'	=> number_format($orderShippingTotal,2),        // Transaction shipping.
            'tt'	=> number_format($orderTax,2),       // Transaction tax.
            'tcc'	=> (string)$order->getCouponCode(), // Transaction coupon code
            'pa'	=> 'purchase' // Product Action
        );

        //adding traffic source data
        $data = $this->addTrafficSourceData($data);

        return $data;
    }

    /**
     * Adding traffic source data to send to GA as part of measurement protocol call
     *
     * @return array
     */
    protected function addTrafficSourceData($data)
    {

        $tsdata = array(
            'cn'	=> $this->_cn, //Campaign Name
            'cs'	=> $this->_cs, //Campaign Source
            'cm'	=> $this->_cm, //Campaign Medium
            'ck'	=> $this->_ck, //Campaign Keyword
            'cc'	=> $this->_cc, //Content
            'gclid' => $this->_gclid //gclid
        );

        $data = array_merge($data,$tsdata);

        return $data;
    }

    /**
     * Retrieving traffic source data for GA from __utmz cookie
     *
     * @return string
     */
    protected function gaParseTSCookie($google_cookie, $storeId=null) {
        // Parse __utmz cookie
        if (isset($google_cookie)){
            list($domain_hash,$timestamp, $session_number, $campaign_numer, $campaign_data) = preg_split('[\.]', $google_cookie,5);

            // Parse the campaign data
            $campaign_data = parse_str(strtr($campaign_data, "|", "&"));

            if (isset($utmcsr)) $this->_cs = $utmcsr;
            if (isset($utmccn)) $this->_cn = $utmccn;
            if (isset($utmcmd)) $this->_cm = $utmcmd;
            if (isset($utmctr)) $this->_ck = $utmctr;
            if (isset($utmcct)) $this->_cc = $utmcct;
            if (isset($utmgclid)) $this->_gclid = $utmgclid;

            // You should tag you campaigns manually to have a full view
            // of your adwords campaigns data.
            // The same happens with Urchin, tag manually to have your campaign data parsed properly.

            if (isset($utmgclid)&&strlen($utmgclid)>0) {
                $this->_cs = "google";
                $this->_cm = "cpc";
            }

        }

        if ($this->isAdmin() && $this->sendPhoneOrderTransaction($storeId)){
            $this->_cs = $this->getSourceText();
            $this->_cm = $this->getMediumText();
        }
    }

    /**
     * return whether you are in admin or not
     *
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->_authSession->getUser()){
            return (bool)(strlen($this->_authSession->getUser()->getUsername()) > 0);
        }
        else{
            return false;
        }
    }

    /**
     * set sent to google data flag against order
     * @param $order /Magento/Sales/Model/Order
     * @return array
     */
    public function setSentDataFlag($order)
    {
        $order->setSendDataToGoogle(1)
            ->save();
    }

    /**
     * build event data for GA
     *
     * @return array
     */
    public function sendEvent($accountId, $cid, $ec,$ea,$el)
    {
        if (strlen($el)){
            $data = array(
                'v' 	=> 1, // The version of the measurement protocol
                'tid' 	=> $accountId, // Google Analytics account ID (UA-98765432-1)
                'cid' 	=> $cid, // The UUID
                't'     => 'event', // Hit Type
                'ec'    => $ec, // Event Category
                'ea'    => $ea, // Event Action
                'el'    => $el, // Event Label
                'ni'	=> 1, // Non-Interaction Hit
            );

            return $data;
        }
    }

    /**
     * Adding page view data to send to GA as part of measurement protocol call
     *
     * @return array
     */
    public function addPageView($accountId, $cid, $dp, $dt, $cos, $col,$pa)
    {
        $domainHost = $this->_domainHost;

        $data = array(
            'v' 	=> 1, // The version of the measurement protocol
            'tid' 	=> $accountId, // Google Analytics account ID (UA-98765432-1)
            'cid' 	=> $cid, // The UUID
            't'     => 'pageview', // Hit type
            'dh'     => $domainHost, // Document Hostname
            'dp'     => $dp, // Page
            'dt'     => $dt, // Page Title
            'pa'	=> $pa, //Action
            'cos'	=> $cos, // Checkout Step
            'col'	=> $col, // Checkout Step Option
        );
        return $data;
    }

    /**
     * Sending transactional data to Google
     * @param $data array
     * @return void
     */
    public function sendDataToGoogle($data)
    {
        if ($data){
            $url = 'https://ssl.google-analytics.com/collect'; // This is the URL to which we'll be sending the post request.
            $content = http_build_query($data); // The body of the post must include exactly 1 URI encoded payload and must be no longer than 8192 bytes. See http_build_query.
            $content = utf8_encode($content); // The payload must be UTF-8 encoded.

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-type: application/x-www-form-urlencoded'));
            curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
            curl_setopt($ch,CURLOPT_POST, TRUE);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $content);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($ch);
            curl_close($ch);

            if ($this->getDebugging()){
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/GA.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($data);
            }
        }
    }

	/**
     * returns license key administration configuration option
     *
     * @return string
     */
    public function getLicenseKey(){
        return $this->scopeConfig->getValue(
            self::XML_PATH_LICENSE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	/**
     * returns whether license key is valid or not
     *
     * @return bool
     */
    public function isLicenseValid(){
		$sku = strtolower(str_replace('\\Helper\\Data','',str_replace('Scommerce\\','',get_class($this))));
		return $this->_data->isLicenseValid($this->getLicenseKey(),$sku);
	}
}