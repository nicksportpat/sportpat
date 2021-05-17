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
use Aitoc\ReviewBooster\Api\Data\Source\CookieHelpfulnessValueInterface;
use InvalidArgumentException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;

class RateAjax extends BaseAjax
{
    const REQUEST_PARAM_NAME_CHOICE = 'choice';

    const CHOICE_VALUE_HELPFUL = 'helpful';
    const CHOICE_VALUE_UNHELPFUL = 'unhelpful';

    /**
     * @var array
     */
    private $helpfulnessRequestToCookieValuesMap = [
        self::CHOICE_VALUE_HELPFUL => CookieHelpfulnessValueInterface::HELPFUL,
        self::CHOICE_VALUE_UNHELPFUL => CookieHelpfulnessValueInterface::UNHELPFUL,
    ];

    /**
     * @param int $reviewId
     * @param RequestInterface $request
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    protected function rememberChangeInCookies($reviewId, RequestInterface $request)
    {
        $cookieValue = $this->getCookieValue($request);
        $this->setCookieReviewUsefulnessValue($reviewId, $cookieValue);
    }

    /**
     * @inheritDoc
     */
    private function getCookieValue(RequestInterface $request)
    {
        $requestedChoice = $this->getRequestedChoice($request);

        return $this->getCookieValueByChoice($requestedChoice);
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    private function getRequestedChoice(RequestInterface $request)
    {
        return $request->getParam(self::REQUEST_PARAM_NAME_CHOICE);
    }

    /**
     * @param string $choice
     * @return int
     */
    private function getCookieValueByChoice($choice)
    {
        return $this->helpfulnessRequestToCookieValuesMap[$choice];
    }

    /**
     * @param int $reviewId
     * @param $cookieValue
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    private function setCookieReviewUsefulnessValue($reviewId, $cookieValue)
    {
        $this->cookiesWriterHelper->setReviewUsefulnessValue($reviewId, $cookieValue);
    }

    /**
     * @inheritDoc
     */
    protected function applyRequestedReviewChanges(ReviewDetailsInterface $review, RequestInterface $request)
    {
        $requestedChoice = $this->getRequestedChoice($request);
        $this->incrementReviewHelpfulnessByChoice($review, $requestedChoice);
    }

    /**
     * @param ReviewDetailsInterface $review
     * @param string $choice
     */
    private function incrementReviewHelpfulnessByChoice(ReviewDetailsInterface $review, $choice)
    {
        switch ($choice) {
            case self::CHOICE_VALUE_HELPFUL:
                $review->incrementHelpful();
                break;
            case self::CHOICE_VALUE_UNHELPFUL:
                $review->incrementUnhelpful();
                break;
            default:
                throw new InvalidArgumentException("Invalid choice value: {$choice}");
        }
    }
}
