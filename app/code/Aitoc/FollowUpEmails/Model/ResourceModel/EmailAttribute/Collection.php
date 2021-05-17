<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Model\ResourceModel\EmailAttribute;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aitoc\FollowUpEmails\Model\EmailAttribute;
use Aitoc\FollowUpEmails\Model\ResourceModel\EmailAttribute as EmailAttributesResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Define model and resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            EmailAttribute::class,
            EmailAttributesResourceModel::class
        );
        parent::_construct();
    }
}
