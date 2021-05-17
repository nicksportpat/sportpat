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

use Magento\Ui\Component\Listing\Columns\Column;
use Aitoc\FollowUpEmails\Api\Data\StatisticInterface;

/**
 * Class CampaignEmails
 */
class CampaignEmails extends Column
{
    /**
     * @var array
     */
    private $statValues = [
        StatisticInterface::SENT,
        StatisticInterface::OPENED,
        StatisticInterface::TRANSITED
    ];
    
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
                if (isset($item['emails'])) {
                    $item['emails'] = $this->updateEmailsData($item['emails']);
                }
            }
        }

        return $dataSource;
    }
    
    /**
     * Prepare Statistic Data
     *
     * @param array & $emailData
     * @return array
     */
    private function updateEmailsData(array $emails)
    {
        foreach ($emails as & $email) {
            if (isset($email['entity_id'])) {
                $email['actions'] = [
                    [
                        'label' => __('Edit'),
                        'href' => $this->context->getUrl(
                            'followup/event_campaignStep/edit',
                            ['id' => $email['entity_id']]
                        ),
                    ],
                    [
                        'label' => __('Delete'),
                        'href' => $this->context->getUrl(
                            'followup/event_campaignStep/delete',
                            ['id' => $email['entity_id']]
                        ),
                        'confirm' => [
                            'title' => __('Delete "${ $.$data.name }"'),
                            'message' => __('Are you sure you wan\'t to delete a "${ $.$data.name }" record?')
                        ]
                    ],
                ];
                
                $this->prepareStatisticData($email);
            }
        }
        
        return $emails;
    }
    
    /**
     * Prepare Statistic Data
     *
     * @param array &$emailData
     */
    private function prepareStatisticData(array &$emailData)
    {
        $defaults = array_fill_keys($this->statValues, 0);
        
        $emailData = array_merge(
            $emailData,
            $defaults,
            array_intersect_key(
                $emailData['statistic'] ?? [],
                $defaults
            )
        );
        
        unset($emailData['statistic']);
        
        $sent  = $emailData[StatisticInterface::SENT] ?? 0;
        $open  = $emailData[StatisticInterface::OPENED] ?? 0;
        $click = $emailData[StatisticInterface::TRANSITED] ?? 0;
        
        $emailData['click_rate'] = $sent ? $click / $sent * 100 : 0;
        $emailData['open_rate']  = $sent ? $open / $sent * 100 : 0;
        
        $emailData['click_rate'] = number_format($emailData['click_rate'], 1) . '%';
        $emailData['open_rate']  = number_format($emailData['open_rate'], 1) . '%';
    }
}
