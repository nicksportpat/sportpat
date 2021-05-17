<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aitoc\ReviewBooster\Api\Data\ImageInterface;

class Image extends AbstractDb
{
    /**
     * Table
     */
    const TABLE = 'aitoc_review_booster_images_collection';

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(self::TABLE, ImageInterface::IMAGE_ID);
    }
}
