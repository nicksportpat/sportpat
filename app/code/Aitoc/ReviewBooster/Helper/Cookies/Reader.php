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
use Magento\Framework\Stdlib\CookieManagerInterface;

class Reader
{
    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * Reader constructor.
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(CookieManagerInterface $cookieManager)
    {
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param int $reviewId
     * @return bool
     */
    public function isReviewUsefulnessRated($reviewId)
    {
        $cookieKey = $this->getUsefulnessCookieKey();

        return $this->isSetCookie($cookieKey, $reviewId);
    }

    /**
     * @param int $reviewId
     * @return int|null
     */
    public function getReviewUsefulnessValue($reviewId)
    {
        $cookieKey = $this->getUsefulnessCookieKey();

        return $this->getCookieValueOrNull($cookieKey, $reviewId);
    }

    /**
     * @param string $cookieKey
     * @param int $reviewId
     * @return int|null
     */
    private function getCookieValueOrNull($cookieKey, $reviewId)
    {
        $typeCookie = $this->getCookie($cookieKey);

        return isset($typeCookie[$reviewId]) ? $typeCookie[$reviewId] : null;
    }

    private function getUsefulnessCookieKey()
    {
        return CookieKeyInterface::USEFULNESS;
    }

    /**
     * @param string $cookieKey
     * @param int $reviewId
     * @return bool
     */
    private function isSetCookie($cookieKey, $reviewId)
    {
        $typeCookie = $this->getCookie($cookieKey);

        return isset($typeCookie[$reviewId]);
    }

    /**
     * @param string $name
     * @return null|string
     */
    private function getCookie($name)
    {
        return $this->cookieManager->getCookie($name);
    }

    public function isReviewAbused($reviewId)
    {
        $cookieKey = $this->getAbusedCookieKey();

        return $this->isSetCookie($cookieKey, $reviewId);
    }

    private function getAbusedCookieKey()
    {
        return CookieKeyInterface::ABUSE;
    }
}