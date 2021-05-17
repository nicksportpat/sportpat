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
use Aitoc\ReviewBooster\Api\ReviewDetailsRepositoryInterface;
use Aitoc\ReviewBooster\Helper\Cookies\Writer as CookiesWriterHelper;
use InvalidArgumentException;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

abstract class BaseAjax extends Action
{
    const REQUEST_PARAM_NAME_REVIEW_ID = 'reviewId';

    /**
     * Ten years
     */
    const COOKIE_DURATION = 315360000;

    /**
     * @var CookiesWriterHelper
     */
    protected $cookiesWriterHelper;

    /**
     * @var ReviewDetailsRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var PublicCookieMetadataFactory
     */
    private $publicCookieMetadataFactory;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * RateAjax constructor.
     * @param Context $context
     * @param ReviewDetailsRepositoryInterface $reviewRepository
     * @param CookiesWriterHelper $cookiesWriterHelper
     * @param PublicCookieMetadataFactory $publicCookieMetadataFactory
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        Context $context,
        ReviewDetailsRepositoryInterface $reviewRepository,
        CookiesWriterHelper $cookiesWriterHelper,
        PublicCookieMetadataFactory $publicCookieMetadataFactory,
        CookieManagerInterface $cookieManager,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->reviewRepository = $reviewRepository;
        $this->cookiesWriterHelper = $cookiesWriterHelper;
        $this->publicCookieMetadataFactory = $publicCookieMetadataFactory;
        $this->cookieManager = $cookieManager;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Send review rate choice
     *
     * @throws CouldNotSaveException
     */
    public function execute()
    {
        $request = $this->_request;
        if (!$request->isAjax()) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $requestedReviewId = $this->getRequestedReviewId($request);

        $review = $this->getAccessibleReviewByIdOrThrow($requestedReviewId);

        $this->applyRequestedReviewChanges($review, $request);
        $this->saveReview($review);
        $this->rememberChangeInCookies($requestedReviewId, $request);
    }

    /**
     * @param RequestInterface $request
     * @return int
     */
    private function getRequestedReviewId(RequestInterface $request)
    {
        return (int)$request->getParam(self::REQUEST_PARAM_NAME_REVIEW_ID);
    }

    /**
     * @param int $reviewId
     * @return ReviewDetailsInterface|null
     */
    private function getAccessibleReviewByIdOrThrow($reviewId)
    {
        $review = $this->getAccessibleReviewById($reviewId);

        if (!$review) {
            throw new InvalidArgumentException("Not accessible review id: {$reviewId}");
        }

        return $review;
    }

    /**
     * @param int $reviewId
     * @return ReviewDetailsInterface|null
     */
    private function getAccessibleReviewById($reviewId)
    {
        return $this->reviewRepository->getAccessibleByReviewId($reviewId);
    }

    /**
     * @param ReviewDetailsInterface $review
     * @param RequestInterface $request
     */
    abstract protected function applyRequestedReviewChanges(ReviewDetailsInterface $review, RequestInterface $request);

    /**
     * @param ReviewDetailsInterface $review
     * @throws CouldNotSaveException
     */
    private function saveReview(ReviewDetailsInterface $review)
    {
        $this->reviewRepository->save($review);
    }

    /**
     * @param int $reviewId
     * @param RequestInterface $request
     */
    abstract protected function rememberChangeInCookies($reviewId, RequestInterface $request);
}
