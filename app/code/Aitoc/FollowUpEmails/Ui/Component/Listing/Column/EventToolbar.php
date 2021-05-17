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

use Aitoc\FollowUpEmails\Api\Data\EventAttributeInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Event\DisplayInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Event\StatusInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class EventAction
 */
class EventToolbar extends Column
{
    const FOLLOW_UP_URL = 'https://www.aitoc.com/en/magento_2_follow_up.html';

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        return $this->prepareItemsData($dataSource);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareItemsData(array $dataSource)
    {
        $srcItems = $dataSource['data']['items'];
        $resultItems = [];

        foreach ($srcItems as $item) {
            $resultItems[] = $this->prepareItemData($item);
        }

        $dataSource['data']['items'] = $resultItems;

        return $dataSource;
    }

    /**
     * @param array $item
     * @return array
     */
    protected function prepareItemData($item)
    {
        if (!isset($item[EventAttributeInterface::CODE]) || !isset($item[EventAttributeInterface::STATUS])) {
            return $item;
        }

        return ($item[EventAttributeInterface::STATUS] == StatusInterface::ACTIVE)
            ? $this->prepareActiveEventData($item)
            : $this->prepareInactiveEventData($item);
    }

    /**
     * @param array $item
     * @return array
     */
    protected function prepareActiveEventData($item)
    {
        return ($item[EventAttributeInterface::DISPLAY] == DisplayInterface::SHOW)
            ? $this->prepareActiveShownEventData($item)
            : $this->prepareActiveHiddenEventData($item);
    }

    /**
     * @param array $item
     * @return array
     */
    protected function prepareActiveShownEventData($item)
    {
        $name = $this->getName();
        $eventCode = $item[EventAttributeInterface::CODE];
        $campaignUrl = $this->getCampaignIndexUrl($eventCode);

        $item[$name] = [
            'manage' => [
                'label' => __('Manage Campaigns'),
                'class' => 'action-basic',
                'href' => $campaignUrl,
            ],
        ];

        return $item;
    }

    /**
     * @param string $eventCode
     * @return string
     */
    protected function getCampaignIndexUrl($eventCode)
    {
        return $this->getUrl(
            'followup/event_campaign/index',
            ['event_code' => $eventCode]
        );
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function getUrl($route, $params)
    {
        return $this->context->getUrl($route, $params);
    }

    /**
     * @param array $item
     * @return array
     */
    protected function prepareActiveHiddenEventData($item)
    {
        $name = $this->getName();

        $item[$name] = [
            'manage' => [],
        ];

        return $item;
    }

    /**
     * @param array $item
     * @return array
     */
    protected function prepareInactiveEventData($item)
    {
        $name = $this->getName();
        $itemUrl = $item['url'] ?? self::FOLLOW_UP_URL;

        $item[$name] = [
            'install' => [
                'label' => __('Install %1', $item['name']),
                'class' => 'action-basic action-activate',
                'href' => $itemUrl,
                'target' => '_blank',
            ],
        ];

        return $item;
    }
}
