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

use Aitoc\ReviewBooster\Api\Data\ImageInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface ImageRepositoryInterface
{
    /**
     * @param ImageInterface $imageModel
     * @return ImageInterface
     * @throws CouldNotSaveException
     */
    public function save(ImageInterface $imageModel);

    /**
     * @param int $itemId
     * @return ImageInterface
     * @throws NoSuchEntityException
     */
    public function get($itemId);

    /**
     * @param int $reviewId
     * @return ImageInterface[]
     */
    public function getByReviewId($reviewId);

    /**
     * @param ImageInterface $imageModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ImageInterface $imageModel);

    /**
     * @param int $itemId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById($itemId);
}
