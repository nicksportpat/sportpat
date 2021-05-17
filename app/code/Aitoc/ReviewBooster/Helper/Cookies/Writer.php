<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Helper\Cookies;

use Aitoc\ReviewBooster\Api\Data\Source\CookieKeyInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Writer
{
    /**
     * Ten years
     */
    const COOKIE_DURATION = 315360000;

    const COOKIE_VALUE_REVIEW_ABUSED = 1;

    /**
     * @var PublicCookieMetadataFactory
     */
    private $publicCookieMetadataFactory;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * Writer constructor.
     * @param PublicCookieMetadataFactory $publicCookieMetadataFactory
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        PublicCookieMetadataFactory $publicCookieMetadataFactory,
        CookieManagerInterface $cookieManager
    ) {
        $this->publicCookieMetadataFactory = $publicCookieMetadataFactory;
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param int $reviewId
     * @param $cookieValue
     * @return void
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     */
    public function setReviewUsefulnessValue($reviewId, $cookieValue)
    {
        $cookieKey = CookieKeyInterface::USEFULNESS;

        $this->setCookieValue($reviewId, $cookieKey, $cookieValue);
    }

    /**
     * @param int $reviewId
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     */
    public function setReviewAbused($reviewId)
    {
        $cookieKey = CookieKeyInterface::USEFULNESS;
        $cookieValue = self::COOKIE_VALUE_REVIEW_ABUSED;

        $this->setCookieValue($reviewId, $cookieKey, $cookieValue);
    }

    /**
     * @param int $reviewId
     * @param string $cookieKey
     * @param $cookieValue
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     */
    private function setCookieValue($reviewId, $cookieKey, $cookieValue)
    {
        $publicCookieMetadata = $this->createCustomChoicePublicCookieMetadata();
        $cookieName = $this->getCookieNameForSave($cookieKey, $reviewId);

        $this->setPublicCookie($cookieName, $cookieValue, $publicCookieMetadata);
    }

    /**
     * @return PublicCookieMetadata
     */
    private function createCustomChoicePublicCookieMetadata()
    {
        $publicCookieMetadata = $this->createPublicCookieMetadata();

        $publicCookieMetadata
            ->setDuration(self::COOKIE_DURATION)
            ->setPath('/')
            ->setHttpOnly(false);

        return $publicCookieMetadata;
    }

    /**
     * @return PublicCookieMetadata
     */
    private function createPublicCookieMetadata()
    {
        return $this->publicCookieMetadataFactory->create();
    }

    /**
     * @param string $cookieKey
     * @param int $reviewId
     * @return string
     */
    private function getCookieNameForSave($cookieKey, $reviewId)
    {
        return "{$cookieKey}[{$reviewId}]";
    }

    /**
     * @param string $cookieName
     * @param mixed $value
     * @param PublicCookieMetadata $metadata
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    private function setPublicCookie($cookieName, $value, PublicCookieMetadata $metadata = null)
    {
        $this->cookieManager->setPublicCookie($cookieName, $value, $metadata);
    }
}
