<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Label\Block\Adminhtml\Labels\Edit;

/**
 * Class Tabs
 * @package Amasty\Label\Block\Adminhtml\Labels\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        array $data = []
    ) {
        $this->cookieManager = $cookieManager;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('amasty_label_labels_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Label Options'));
    }

    public function _beforeToHtml()
    {
        $activeTab = $this->cookieManager->getCookie('amasty_labels_current_tab');
        if ($activeTab) {
            $this->setActiveTab(str_replace('amasty_label_labels_edit_tabs_', '', $activeTab));
            $this->setActiveTabId($activeTab);
        }
        return parent::_beforeToHtml();
    }
}
