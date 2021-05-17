<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Api;

use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface ReviewDetailsRepositoryInterface
{
    /**
     * @param ReviewDetailsInterface $reviewModel
     * @return ReviewDetailsInterface
     * @throws CouldNotSaveException
     */
    public function save(ReviewDetailsInterface $reviewModel);

    /**
     * @param int $itemId
     * @return ReviewDetailsInterface
     * @throws NoSuchEntityException
     */
    public function get($itemId);

    /**
     * @param int $reviewId
     * @return ReviewDetailsInterface
     */
    public function getByReviewId($reviewId);

    /**
     * @param int $reviewId
     * @return ReviewDetailsInterface|null
     */
    public function getAccessibleByReviewId($reviewId);

    /**
     * @param ReviewDetailsInterface $reviewModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ReviewDetailsInterface $reviewModel);

    /**
     * @param int $itemId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById($itemId);
}
