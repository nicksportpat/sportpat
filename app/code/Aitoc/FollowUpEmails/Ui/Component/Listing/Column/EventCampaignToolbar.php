<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Ui\Component\Listing\Column;

use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignTableInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class EventCampaignAction
 */
class EventCampaignToolbar extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[CampaignTableInterface::COLUMN_NAME_ENTITY_ID])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'label' => __('Edit Campaign'),
                            'href' => $this->context->getUrl(
                                'followup/event_campaign/edit',
                                ['id' => $item[CampaignTableInterface::COLUMN_NAME_ENTITY_ID]]
                            ),
                        ],
                        'delete' => [
                            'label' => __('Delete'),
                            'href' => $this->context->getUrl(
                                'followup/event_campaign/deleteCampaign',
                                ['id' => $item[CampaignTableInterface::COLUMN_NAME_ENTITY_ID]]
                            ),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.name }"'),
                                'message' => __('Are you sure you wan\'t to delete a "${ $.$data.name }" record?')
                            ]
                        ],
                        'add_email' => [
                            'label' => __('Add Email'),
                            'class' => 'action-basic',
                            'href' => $this->context->getUrl(
                                'followup/event_campaignStep/edit',
                                ['campaign_id' => $item[CampaignTableInterface::COLUMN_NAME_ENTITY_ID]]
                            ),
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
