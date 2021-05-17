<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Plugin;

use Aitoc\ReviewBooster\Api\Data\ImageInterface;
use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;
use Aitoc\ReviewBooster\Api\ImageRepositoryInterface;
use Aitoc\ReviewBooster\Api\ReviewDetailsRepositoryInterface;
use Aitoc\ReviewBooster\Model\ResourceModel\Image\Collection as ImageCollection;
use Aitoc\ReviewBooster\Model\ReviewDetailsRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Review\Model\Review as ReviewModel;

/**
 * Class Review
 */
class Review
{
    const REQUESTED_PARAM_NAME_REVIEW_ID = 'reviewId';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ImageCollection
     */
    protected $imageCollection;

    /**
     * @var ReviewDetailsRepository
     */
    protected $reviewDetailsRepository;

    /**
     * @var ImageRepositoryInterface
     */
    private $imageRepository;

    /**
     * @param RequestInterface $request
     * @param ImageCollection $imageCollection
     * @param ReviewDetailsRepositoryInterface $reviewDetailsRepository
     * @param ImageRepositoryInterface $imageRepository
     */
    public function __construct(
        RequestInterface $request,
        ImageCollection $imageCollection,
        ReviewDetailsRepositoryInterface $reviewDetailsRepository,
        ImageRepositoryInterface $imageRepository
    ) {
        $this->request = $request;
        $this->imageCollection = $imageCollection;
        $this->reviewDetailsRepository = $reviewDetailsRepository;
        $this->imageRepository = $imageRepository;
    }

    /**
     * Add extended data to a loaded review
     *
     * @param ReviewModel $review
     * @return ReviewModel
     */
    public function afterLoad(ReviewModel $review)
    {
        $reviewId = $this->getResultReviewId($review);

        if (!$reviewId) {
            return $review;
        }

        $this->addImagesDataIfExists($review, $reviewId);
        $this->addReviewDetailsDataIfExists($review, $reviewId);

        return $review;
    }

    /**
     * @param ReviewModel $review
     * @return int|mixed|null
     */
    private function getResultReviewId(ReviewModel $review)
    {
        $reviewOriginalId = $review->getId();
        $reviewRequestId = $this->getRequestedReviewId();

        $reviewId = null;

        //q: how in afterLoad() original review id can be empty?
        if ($reviewOriginalId) {
            $reviewId = $reviewOriginalId;
        } elseif ($reviewRequestId) {
            $reviewId = $reviewRequestId;
        }
        return $reviewId;
    }

    /**
     * @return int
     */
    private function getRequestedReviewId()
    {
        return $this->request->getParam(self::REQUESTED_PARAM_NAME_REVIEW_ID);
    }

    /**
     * @param ReviewModel $review
     * @param int $reviewId
     */
    private function addImagesDataIfExists(ReviewModel $review, $reviewId)
    {
        $images = $this->getImagesByReviewId($reviewId);

        if (!$images) {
            return;
        }

        $imagesData = $this->getImagesDataByImages($images);
        $review->addData($imagesData);
    }

    /**
     * @param $reviewId
     * @return ImageInterface[]
     */
    private function getImagesByReviewId($reviewId)
    {
        return $this->imageRepository->getByReviewId($reviewId);
    }

    /**
     * @param ImageInterface[] $images
     * @return array
     */
    private function getImagesDataByImages($images)
    {
        $imageArray = [];

        foreach ($images as $key => $item) {
            $imageArray['image' . '_' . $key] = $item->getImage();
        }

        return $imageArray;
    }

    /**
     * @param ReviewModel $review
     * @param int $reviewId
     */
    private function addReviewDetailsDataIfExists(ReviewModel $review, $reviewId)
    {
        $reviewDetails = $this->getReviewDetailsByReviewId($reviewId);

        if (!$reviewDetails) {
            return;
        }

        $extendedData = $reviewDetails->getData();
        $review->addData($extendedData);
    }

    /**
     * @param $reviewId
     * @return ReviewDetailsInterface
     */
    private function getReviewDetailsByReviewId($reviewId)
    {
        return $this->reviewDetailsRepository->getByReviewId($reviewId);
    }
}
