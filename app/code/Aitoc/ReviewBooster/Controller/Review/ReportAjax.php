<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Controller\Review;

use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;
use Aitoc\ReviewBooster\Api\Data\Source\CookieKeyInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;

class ReportAjax extends BaseAjax
{
    const COOKIE_VALUE = 1;

    /**
     * @inheritDoc
     */
    protected function getCookieKey()
    {
        return CookieKeyInterface::ABUSE;
    }

    /**
     * @inheritDoc
     */
    protected function applyRequestedReviewChanges(ReviewDetailsInterface $review, RequestInterface $request)
    {
        $review->incrementReviewReported();
    }

    /**
     * @param int $reviewId
     * @param RequestInterface $request
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     */
    protected function rememberChangeInCookies($reviewId, RequestInterface $request)
    {
        $this->setCookieReviewUsefulnessValue($reviewId);
    }

    /**
     * @param int $reviewId
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    private function setCookieReviewUsefulnessValue($reviewId)
    {
        $this->cookiesWriterHelper->setReviewAbused($reviewId);
    }
}
