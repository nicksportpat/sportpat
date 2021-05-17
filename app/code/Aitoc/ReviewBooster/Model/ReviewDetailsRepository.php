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

use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;
use Aitoc\ReviewBooster\Api\ReviewDetailsRepositoryInterface;
use Aitoc\ReviewBooster\Model\ResourceModel\ReviewDetails as ReviewDetailsResource;
use Aitoc\ReviewBooster\Model\ReviewDetailsFactory as ReviewDetailsModelFactory;
use Aitoc\ReviewBooster\Api\Data\Source\Table\ReviewInterface as ReviewTableInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Review\Model\Review as MagentoReview;

/**
 * Class ReviewDetailsRepository
 */
class ReviewDetailsRepository implements ReviewDetailsRepositoryInterface
{
    /**
     * @var ReviewDetailsResource
     */
    protected $reviewDetailsResource;

    /**
     * @var ReviewDetailsModelFactory
     */
    protected $reviewDetailsModelFactory;

    /**
     * @var MagentoReviewManagement
     */
    private $magentoReviewManagement;

    /**
     * @param ReviewDetailsResource $reviewDetailsResource
     * @param ReviewDetailsModelFactory $reviewDetailsModelFactory
     * @param MagentoReviewManagement $magentoReviewManagement
     */
    public function __construct(
        ReviewDetailsResource $reviewDetailsResource,
        ReviewDetailsModelFactory $reviewDetailsModelFactory,
        MagentoReviewManagement $magentoReviewManagement
    ) {
        $this->reviewDetailsResource = $reviewDetailsResource;
        $this->reviewDetailsModelFactory = $reviewDetailsModelFactory;
        $this->magentoReviewManagement = $magentoReviewManagement;
    }

    /**
     * @param ReviewDetailsInterface|AbstractModel $reviewModel
     * @return ReviewDetailsInterface|object
     * @throws CouldNotSaveException
     */
    public function save(ReviewDetailsInterface $reviewModel)
    {
        try {
            $this->reviewDetailsResource->save($reviewModel);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $reviewModel->getEntityId()));
        }

        return $reviewModel;
    }

    /**
     * @param int $reviewId
     * @return ReviewDetailsInterface|null
     * @throws NoSuchEntityException
     */
    public function getAccessibleByReviewId($reviewId)
    {
        $magentoReview = $this->getAccessibleMagentoReviewByReviewId($reviewId);

        return $magentoReview ? $this->getByReviewId($reviewId) : null;
    }

    /**
     * @param $reviewId
     * @return MagentoReview|null
     * @throws NoSuchEntityException
     */
    private function getAccessibleMagentoReviewByReviewId($reviewId)
    {
        return $this->magentoReviewManagement->getAccessibleById($reviewId);
    }

    /**
     * @param int $reviewId
     * @return ReviewDetailsInterface
     */
    public function getByReviewId($reviewId)
    {
        $reviewModel = $this->reviewDetailsModelFactory->create();
        $this->reviewDetailsResource->load($reviewModel, $reviewId, ReviewTableInterface::COLUMN_NAME_REVIEW_ID);

        return $reviewModel->getId() ? $reviewModel : null;
    }

    /**
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($entityId)
    {
        $model = $this->get($entityId);
        $this->delete($model);

        return true;
    }

    /**
     * @param int $entityId
     * @return ReviewDetails
     * @throws NoSuchEntityException
     */
    public function get($entityId)
    {
        $reviewModel = $this->reviewDetailsModelFactory->create();
        $this->reviewDetailsResource->load($reviewModel, $entityId);

        if (!$reviewModel->getEntityId()) {
            throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $entityId));
        }

        return $reviewModel;
    }

    /**
     * @param ReviewDetailsInterface|AbstractModel $reviewModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ReviewDetailsInterface $reviewModel)
    {
        try {
            $this->reviewDetailsResource->delete($reviewModel);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID%', $reviewModel->getEntityId()));
        }

        return true;
    }
}
