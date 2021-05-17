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

use Aitoc\ReviewBooster\Api\Data\ImageInterface;
use Magento\Framework\Model\AbstractModel;
use Aitoc\ReviewBooster\Model\ResourceModel\Image as ImageResourceModel;

class Image extends AbstractModel implements ImageInterface
{

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ImageResourceModel::class);
    }

    /**
     * @return int imageId
     */
    public function getImageId()
    {
        return $this->getData(self::IMAGE_ID);
    }

    /**
     * @param int imageId
     *
     * @return $this
     */
    public function setImageId($imageId)
    {
        return $this->setData(self::IMAGE_ID, $imageId);
    }

    /**
     * @return int reviewId
     */
    public function getReviewId()
    {
        return $this->getData(self::REVIEW_ID);
    }

    /**
     * @param int reviewId
     *
     * @return $this
     */
    public function setReviewId($reviewId)
    {
        return $this->setData(self::REVIEW_ID, $reviewId);
    }

    /**
     * @return string image
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @param string image
     *
     * @return $this
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }
}
