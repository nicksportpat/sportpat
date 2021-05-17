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

use Aitoc\ReviewBooster\Helper\Cookies\Reader as CookiesReaderHelper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class ReportAbuse extends AbstractHelper
{
    const ROUTE_PATH = 'aitocreviewbooster/review/reportAjax';

    /**
     * Report review message
     */
    const MESSAGE_REPORTED = 'You reported this review.';

    /**
     * @var CookiesReaderHelper
     */
    private $cookiesReaderHelper;

    /**
     * Report constructor.
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
     * Check is review reported
     *
     * @param int $reviewId
     * @return int
     */
    public function isReviewReported($reviewId)
    {
        return $this->cookiesReaderHelper->isReviewAbused($reviewId);
    }

    /**
     * Get report URL
     *
     * @return string
     */
    public function getReportUrl()
    {
        return $this->_getUrl(self::ROUTE_PATH);
    }

    /**
     * Get reported message
     *
     * @return string
     */
    public function getReportedMessage()
    {
        return self::MESSAGE_REPORTED;
    }
}
