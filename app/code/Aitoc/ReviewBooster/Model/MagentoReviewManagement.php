<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Model;

use Aitoc\ReviewBooster\Api\Data\CoreReviewInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Review as ReviewResource;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class MagentoReviewManagement
{
    /**
     * @var ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var ReviewResource
     */
    private $reviewResource;

    /**
     * @var ReviewCollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * MagentoReviewManagement constructor.
     * @param ReviewFactory $reviewFactory
     * @param ReviewResource $reviewResource
     * @param ReviewCollectionFactory $reviewCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ReviewFactory $reviewFactory,
        ReviewResource $reviewResource,
        ReviewCollectionFactory $reviewCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->reviewResource = $reviewResource;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getApprovedCountByProductId($productId)
    {
        $reviewCollection = $this->createReviewCollection();

        $reviewCollection
            ->addFieldToFilter(CoreReviewInterface::STATUS_ID, Review::STATUS_APPROVED)
            ->addEntityFilter(CoreReviewInterface::PRODUCT, $productId)
        ;

        return $reviewCollection->getSize();
    }

    /**
     * @param int $reviewId
     * @return Review|null
     */
    public function getById($reviewId)
    {
        $review = $this->createReviewModel();

        $this->reviewResource->load($review, $reviewId);

        return $review->getId() ? $review : null;
    }

    /**
     * @param $reviewId
     * @return Review|null
     * @throws NoSuchEntityException
     */
    public function getAccessibleById($reviewId)
    {
        $review = $this->getById($reviewId);

        $store = $this->getStore();

        return ($review && $review->isApproved() && $review->isAvailableOnStore($store))
            ? $review
            : null;
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    protected function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @return Review
     */
    private function createReviewModel()
    {
        return $this->reviewFactory->create();
    }

    /**
     * @return ReviewCollection
     */
    private function createReviewCollection()
    {
        return $this->reviewCollectionFactory->create();
    }
}
