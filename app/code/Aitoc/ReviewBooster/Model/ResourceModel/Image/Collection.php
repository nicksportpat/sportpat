<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Model\ResourceModel\Image;

use Aitoc\ReviewBooster\Api\Data\ImageInterface;
use Aitoc\ReviewBooster\Model\Image;
use Aitoc\ReviewBooster\Model\ResourceModel\Image as ImageResourceModel;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = ImageInterface::IMAGE_ID;

    /**
     * Define resource model and model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Image::class, ImageResourceModel::class);
        parent::_construct();
    }
}
