<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */


namespace Aitoc\FollowUpEmails\Plugin\Model;

use Magento\Email\Model\Template;
use Aitoc\FollowUpEmails\Service\EmailManagement;
use Aitoc\FollowUpEmails\Api\Contoller\Account\Login\RequestParamNameInterface;
use Aitoc\FollowUpEmails\Helper\Campaign;
use Aitoc\FollowUpEmails\Helper\Url;

class UrlModifier
{
    /**
     * @var Campaign
     */
    private $campaign;

    /**
     * @var Url
     */
    private $url;

    public function __construct(
        Campaign $campaign,
        Url $url
    ) {
        $this->campaign = $campaign;
        $this->url = $url;
    }

    /**
     * @param \Magento\Email\Model\AbstractTemplate $subject
     * @param \Closure $proceed
     * @param \Magento\Store\Model\Store $store
     * @param string $route
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     */
    public function aroundGetUrl(
        \Magento\Email\Model\AbstractTemplate $subject,
        \Closure $proceed,
        \Magento\Store\Model\Store $store,
        $route = '',
        $params = []
    ) {
        $reflection = new \ReflectionProperty(Template::class, '_vars');
        $reflection->setAccessible(true);
        $templateVars = $reflection->getValue($subject);
        $resultPlugin = $proceed($store, $route, $params);

        if ($templateVars
            && isset($templateVars[EmailManagement::AITOC_FOLLOW_UP_IDENTIFIER])
            && isset($templateVars[RequestParamNameInterface::EMAIL_ID])
        ) {
            return $this->url->prepareUtmByEmailId($resultPlugin, $templateVars[RequestParamNameInterface::EMAIL_ID]);
        }

        return $resultPlugin;
    }
}
