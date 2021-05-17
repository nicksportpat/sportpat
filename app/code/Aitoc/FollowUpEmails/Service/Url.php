<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Service;

use Magento\Backend\Model\UrlInterface;

/**
 * Class Url
 */
class Url
{
    const ROUTE_PATH_UNSUBSCRIBE = 'followup/email/unsubscribe';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        return $this->getUrl(self::ROUTE_PATH_UNSUBSCRIBE);
    }

    /**
     * @param string $routePath
     * @return string
     */
    private function getUrl($routePath)
    {
        return $this->urlBuilder->getUrl($routePath);
    }
}
