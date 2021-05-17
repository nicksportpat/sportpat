<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Model\ResourceModel\Email;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aitoc\FollowUpEmails\Model\Email;
use Aitoc\FollowUpEmails\Model\ResourceModel\Email as EmailsResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define model and resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            Email::class,
            EmailsResourceModel::class
        );
        parent::_construct();
    }
}
