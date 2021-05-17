<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Block\Adminhtml\Campaign;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;

class AddButton implements ButtonProviderInterface
{
    const PARAM = 'event_code';
    
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * CustomButton constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->request    = $context->getRequest();
        $this->urlBuilder = $context->getUrlBuilder();
    }
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label'    => __('Add Campaign'),
            'class'    => 'primary',
            'id'       => 'add_new_event_campaign',
            'on_click' => sprintf("location.href = '%s';", $this->getAddCampaignUrl()),
        ];
        
        return $data;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
    
    /**
     * @return int|null
     */
    public function getAddCampaignUrl()
    {
        $params = null;
        if ($this->request->getParam(self::PARAM)) {
            $params = [self::PARAM => $this->request->getParam(self::PARAM)];
        }
        
        return $this->getUrl('followup/event_campaign/new', $params);
    }
}
