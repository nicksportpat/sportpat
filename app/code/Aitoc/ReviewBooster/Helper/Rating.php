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

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;

class Rating extends AbstractHelper
{
    const REQUEST_PARAM_NAME_RATING = 'rating';

    const PRODUCT_DATA_KEY_RATING_SUMMARY = 'rating_summary';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Rating constructor.
     * @param Context $context
     * @param RequestInterface $request
     * @param ReviewFactory $reviewFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        ReviewFactory $reviewFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->reviewFactory = $reviewFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Get rating param from URL
     *
     * @return int
     */
    public function getRequestedRating()
    {
        return (int) $this->getRequestedParam(self::REQUEST_PARAM_NAME_RATING);
    }

    /**
     * Validate rating param from URL
     *
     * @param int $ratingValue
     * @return bool
     */
    public function isValidRatingValue($ratingValue)
    {
        if (!$ratingValue || $ratingValue < 1 || $ratingValue > 5) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Convert rating from percents to points
     *
     * @param float $rating
     * @param bool $ceil
     * @return int
     */
    public function convertRatingToPoints($rating, $ceil = false)
    {
        if ($rating <= 20) {
            return 1;
        }

        $convertedRating = $rating * 0.1 / 2;

        if ($ceil) {
            $convertedRating = ceil($convertedRating);
        }

        if ($convertedRating < 1) {
            $convertedRating = 1;
        } elseif ($convertedRating > 5) {
            $convertedRating = 5;
        }

        return $convertedRating;
    }

    /**
     * @param string $paramName
     */
    private function getRequestedParam($paramName)
    {
        $this->request->getParam($paramName);
    }

    /**
     * Get detailed product rating
     *
     * @param ReviewCollection $reviewsCollection
     * @return array
     */
    public function getDetailedRating(ReviewCollection $reviewsCollection)
    {
        $detailedRating = array_fill(1, 5, 0);

        foreach ($reviewsCollection as $review) {
            if (isset($review['percent'])) {
                foreach (explode(',', $review['percent']) as $vote) {
                    $voteValue = $this->convertRatingToPoints($vote, true);
                    $detailedRating[$voteValue]++;
                }
            }
        }

        return array_reverse($detailedRating, true);
    }

    /**
     * Get rating summary
     *
     * @param ProductInterface|Product $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function getRatingSummary(ProductInterface $product)
    {
        $this->reviewFactory
            ->create()
            ->getEntitySummary($product, $this->storeManager->getStore()->getId());

        return $product->getData(self::PRODUCT_DATA_KEY_RATING_SUMMARY)->getRatingSummary();
    }

    /**
     * Calculate single rating
     *
     * @param float $totalRatings
     * @param float $singleRating
     * @return float
     */
    public function calculateSingleRating($totalRatings, $singleRating)
    {
        if ($totalRatings < 1 || $singleRating < 1) {
            return 0;
        } else {
            return round($singleRating / $totalRatings * 100);
        }
    }
}
