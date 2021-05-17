<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Model\ResourceModel;

use Aitoc\FollowUpEmails\Api\Setup\Current\EmailAttributeTableInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class EmailAttribute
 */
class EmailAttribute extends AbstractDb
{
    /**
     * Table
     */
    const TABLE = 'aitoc_follow_up_emails_email_attributes';

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            EmailAttributeTableInterface::TABLE_NAME,
            EmailAttributeTableInterface::COLUMN_NAME_ENTITY_ID
        );
    }
}
