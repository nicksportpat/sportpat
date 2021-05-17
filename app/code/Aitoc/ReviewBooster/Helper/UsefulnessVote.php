<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Helper;

use Aitoc\ReviewBooster\Api\Data\Source\CookieHelpfulnessValueInterface;
use Aitoc\ReviewBooster\Helper\Cookies\Reader as CookiesReaderHelper;
use LogicException;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class UsefulnessVote extends AbstractHelper
{
    /**
     * Helpful review message
     */
    const MESSAGE_HELPFUL = 'You have found this helpful.';

    /**
     * Unhelpful review message
     */
    const MESSAGE_UNHELPFUL = 'You have found this unhelpful.';

    /**
     * @var CookiesReaderHelper
     */
    private $cookiesReaderHelper;

    /**
     * Rate constructor.
     * @param Context $context
     * @param CookiesReaderHelper $cookiesReaderHelper
     */
    public function __construct(
        Context $context,
        CookiesReaderHelper $cookiesReaderHelper
    ) {
        parent::__construct($context);
        $this->cookiesReaderHelper = $cookiesReaderHelper;
    }

    /**
     * Get rate URL
     *
     * @return string
     */
    public function getRateUrl()
    {
        return $this->_getUrl('aitocreviewbooster/review/rateAjax');
    }

    /**
     * Check is review rated
     *
     * @param int $reviewId
     * @return bool
     */
    public function isReviewRated($reviewId)
    {
        return $this->cookiesReaderHelper->isReviewUsefulnessRated($reviewId);
    }

    /**
     * Get rate choice message
     *
     * @param int $reviewId
     * @return string
     */
    public function getChoiceMessage($reviewId)
    {
        $usefulnessCookieValue = $this->usefulnessCookieValue($reviewId);

        switch ($usefulnessCookieValue) {
            case CookieHelpfulnessValueInterface::HELPFUL:
                $choiceMessage = $this->getHelpfulMessage();
                break;
            case CookieHelpfulnessValueInterface::UNHELPFUL:
                $choiceMessage = $this->getUnhelpfulMessage();
                break;
            default:
                throw new LogicException(sprintf("No Usefulness cookie value for review id: %d", $reviewId));
        }

        return $choiceMessage;
    }

    /**
     * @param int $reviewId
     * @return int|null
     */
    private function usefulnessCookieValue($reviewId)
    {
        return $this->cookiesReaderHelper->getReviewUsefulnessValue($reviewId);
    }

    /**
     * Get helpful message
     *
     * @return string
     */
    public function getHelpfulMessage()
    {
        return self::MESSAGE_HELPFUL;
    }

    /**
     * Get unhelpful message
     *
     * @return string
     */
    public function getUnhelpfulMessage()
    {
        return self::MESSAGE_UNHELPFUL;
    }
}
