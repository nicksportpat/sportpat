<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Cron;

use Aitoc\FollowUpEmails\Model\StatisticManagement;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class StatisticProcess
{
    /**
     * @var StatisticManagement
     */
    private $statisticManagement;

    /**
     * @param StatisticManagement $statisticManagement
     */
    public function __construct(StatisticManagement $statisticManagement)
    {
        $this->statisticManagement = $statisticManagement;
    }

    /**
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function updateStatistic()
    {
        $this->statisticManagement->updateStatistic();
    }
}
