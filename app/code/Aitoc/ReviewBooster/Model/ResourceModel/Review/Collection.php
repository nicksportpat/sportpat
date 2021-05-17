<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Model\ResourceModel\Review;

use Magento\Backend\App\ConfigInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aitoc\ReviewBooster\Model\ReviewDetails;
use Aitoc\ReviewBooster\Model\ResourceModel\ReviewDetails as ReviewResourceModel;
use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;

class Collection extends AbstractCollection
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = ReviewDetailsInterface::EXTENDED_ID;


    /**
     * Define resource model and model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ReviewDetails::class, ReviewResourceModel::class);
        parent::_construct();
    }
}
