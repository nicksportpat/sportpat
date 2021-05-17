<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\TransitTo\Base;

use Aitoc\FollowUpEmails\Api\Contoller\Account\Login\RequestParamNameInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Controller\TransitTo\Base as BaseTransitTiAction;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * Class Homepage
 */
abstract class Homepage extends BaseTransitTiAction
{
    /**
     * @param EmailInterface $email
     */
    protected function afterAutoLogin(EmailInterface $email)
    {

    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    protected function getResultRedirect(RequestInterface $request)
    {
        $requestedAnchor = $this->getRequestedAnchor($request);

        return $this->createRedirectToHomepage($requestedAnchor);
    }

    /**
     * @param RequestInterface $request
     * @return string|null
     */
    protected function getRequestedAnchor(RequestInterface $request)
    {
        return $request->getParam(RequestParamNameInterface::ANCHOR);
    }

    /**
     * @param string|null $anchor
     * @return ResponseInterface
     */
    protected function createRedirectToHomepage($anchor = null)
    {
        $url = $this->_url->getUrl('/');

        if ($anchor) {
            $url = $this->addAnchorToUrl($url, $anchor);
        }

        return $this->_redirect($url);
    }
}
