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

use Aitoc\FollowUpEmails\Api\Setup\Current\StatisticTableInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Statistic
 */
class Statistic extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            StatisticTableInterface::TABLE_NAME,
            StatisticTableInterface::COLUMN_NAME_ENTITY_ID
        );
    }
}
