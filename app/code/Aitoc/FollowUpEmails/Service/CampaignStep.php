<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Service;

use Aitoc\FollowUpEmails\Api\Data\Source\Campaign\StatusInterface as CampaignStatusInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\CampaignStep\StatusInterface as CampaignStepStatusInterface;
use Aitoc\FollowUpEmails\Api\EventManagementInterface;
use Aitoc\FollowUpEmails\Api\Service\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignStepTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignTableInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class CampaignStep
 */
class CampaignStep implements CampaignStepInterface
{
    /**
     * @var EventManagementInterface
     */
    private $eventManagement;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * CampaignStep constructor.
     * @param EventManagementInterface $eventManagement
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        EventManagementInterface $eventManagement,
        ResourceConnection $resourceConnection
    ) {
        $this->eventManagement = $eventManagement;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return int[]
     */
    public function getActiveCampaignStepsIds()
    {
        $activeEventsCodes = $this->getActiveEventsCodes();

        $dbAdapter = $this->getDbAdapter();

        $select = $dbAdapter->select();

        $campaignTableAlias = 'campaign';
        $prefixedCampaignTableName = $this->getPrefixedTableName(CampaignTableInterface::TABLE_NAME);
        $campaignEventCode = CampaignTableInterface::COLUMN_NAME_EVENT_CODE;
        $campaignStatus = CampaignTableInterface::COLUMN_NAME_STATUS;
        $campaignId = CampaignTableInterface::COLUMN_NAME_ENTITY_ID;

        $campaignStepTableAlias = 'campaign_step';
        $prefixedCampaignStepTableName = $this->getPrefixedTableName(CampaignStepTableInterface::TABLE_NAME);
        $campaignStepStatus = CampaignStepTableInterface::COLUMN_NAME_STATUS;
        $campaignStepCampaignId = CampaignStepTableInterface::COLUMN_NAME_CAMPAIGN_ID;
        $campaignStepId = CampaignStepTableInterface::COLUMN_NAME_ENTITY_ID;

        $select
            ->from([$campaignTableAlias => $prefixedCampaignTableName], [])
            ->join(
                [$campaignStepTableAlias => $prefixedCampaignStepTableName],
                "{$campaignStepTableAlias}.{$campaignStepCampaignId} = {$campaignTableAlias}.{$campaignId}",
                [$campaignStepId]
            )
            ->where("{$campaignTableAlias}.{$campaignEventCode} IN (?)", $activeEventsCodes)
            ->where("{$campaignTableAlias}.{$campaignStatus} = ?", CampaignStatusInterface::ENABLED)
            ->where("{$campaignStepTableAlias}.{$campaignStepStatus} = ?", CampaignStepStatusInterface::ENABLED)
        ;

        return $dbAdapter->fetchCol($select);
    }

    /**
     * @param string $tableName
     * @return string
     */
    private function getPrefixedTableName($tableName)
    {
        return $this->resourceConnection->getTableName($tableName);
    }

    /**
     * @return string[]
     */
    private function getActiveEventsCodes()
    {
        return $this->eventManagement->getActiveEventsCodes();
    }

    /**
     * @return AdapterInterface
     */
    private function getDbAdapter()
    {
        return $this->resourceConnection->getConnection();
    }

}