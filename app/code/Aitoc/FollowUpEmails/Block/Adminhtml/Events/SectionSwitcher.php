<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Block\Adminhtml\Events;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\WebsiteFactory;

/**
 * Switcher block
 */
class SectionSwitcher extends Template
{
    /**
     * Website factory
     *
     * @var WebsiteFactory
     */
    protected $request;

    /**
     * @param Context $context
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
    }

    /**
     * Website factory
     *
     * @var WebsiteFactory
     * @return int
     */
    public function getCurrentPageName()
    {
        $links = $this->getData('links');
        $links = array_combine(array_column($links, 'url'), $links);
        
        if (isset($links[$this->request->getFullActionName('/')])) {
            return $links[$this->request->getFullActionName('/')]['text'];
        }
        
        return $this->getDefaultName();
    }

    /**
     * Website factory
     *
     * @var WebsiteFactory
     * @return string
     */
    public function getDefaultName()
    {
        return $this->getData('default.name');
    }

    /**
     * Website factory
     *
     * @var WebsiteFactory
     * @return array
     */
    public function getLinks()
    {
        return $this->getData('links');
    }

    /**
     * Website factory
     *
     * @var WebsiteFactory
     * @return string
     */
    public function getLinkUrl($link)
    {
        return $this->getUrl($link['url']);
    }

    /**
     * Website factory
     *
     * @var WebsiteFactory
     * @return string
     */
    public function getLinkText($link)
    {
        return $link['text'];
    }
}
