<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Model\ResourceModel\UnsubscribedEmailAddress;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aitoc\FollowUpEmails\Model\UnsubscribedEmailAddress;
use Aitoc\FollowUpEmails\Model\ResourceModel\UnsubscribedEmailAddress as UnsubscribedEmailAddressModel;

/**
 * Class Collection
 */
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
            UnsubscribedEmailAddress::class,
            UnsubscribedEmailAddressModel::class
        );
        parent::_construct();
    }
}
