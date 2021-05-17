<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Api\Data;

interface ImageInterface
{
    /**
     * Constants defined for keys of data array
     */
    const IMAGE_ID = 'image_id';
    const REVIEW_ID = 'review_id';
    const IMAGE = 'image';

    /**
     * @return int
     */
    public function getImageId();

    /**
     * @param int $imageId
     * @return $this
     */
    public function setImageId($imageId);

    /**
     * @return int
     */
    public function getReviewId();

    /**
     * @param int $reviewId
     * @return $this
     */
    public function setReviewId($reviewId);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $image
     * @return $this
     */
    public function setImage($image);
}
