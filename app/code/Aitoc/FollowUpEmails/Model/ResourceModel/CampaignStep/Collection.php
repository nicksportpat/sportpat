<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Model\ResourceModel\CampaignStep;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aitoc\FollowUpEmails\Model\CampaignStep;
use Aitoc\FollowUpEmails\Model\ResourceModel\CampaignStep as CampaignStepsResourceModel;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignStepTableInterface;

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
            CampaignStep::class,
            CampaignStepsResourceModel::class
        );
        parent::_construct();
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            $options = $item->getData(CampaignStepTableInterface::COLUMN_NAME_OPTIONS);
            if (is_string($options)) {
                $options = json_decode($options, true);
            }
            $item->setData(CampaignStepTableInterface::COLUMN_NAME_OPTIONS, $options);
            $item->setDataChanges(false);
        }
        return $this;
    }
}
