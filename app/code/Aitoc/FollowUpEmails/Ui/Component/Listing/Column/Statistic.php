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
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Aitoc\FollowUpEmails\Model\EventManagement;
use Aitoc\FollowUpEmails\Model\CampaignRepository;
use Aitoc\FollowUpEmails\Model\CampaignStepRepository;
use Aitoc\FollowUpEmails\Api\Data\StatisticInterface  as SI;

/**
 * Class Statistic
 */
class Statistic extends Column
{
    /**
     * @var EventManagement
     */
    private $eventManager;

    /**
     * @var CampaignRepository
     */
    private $campaignsRepository;

    /**
     * @var CampaignStepRepository
     */
    private $campaignStepsRepository;
    
    /**
     * @var PriceCurrencyInterface
     */
    private $price;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param EventManagement $eventManager
     * @param CampaignRepository $campaignsRepository
     * @param CampaignStepRepository $campaignStepsRepository
     * @param PriceCurrencyInterface $price,
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        EventManagement $eventManager,
        CampaignRepository $campaignsRepository,
        CampaignStepRepository $campaignStepsRepository,
        PriceCurrencyInterface $price,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->eventManager = $eventManager;
        $this->campaignsRepository = $campaignsRepository;
        $this->campaignStepsRepository = $campaignStepsRepository;
        $this->price = $price;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['statistics'])) {
                    $item['statistics'] = $this->regroupStatistic($item['statistics']);
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param int $evenCount
     * @param int $value
     * @return string
     */
    private function ctr($evenCount, $value)
    {
        $ctr = 0;
        if ($value > 0) {
            $ctr = $value / $evenCount * 100;
        }
        
        return number_format($ctr, 1);
    }
    
    /**
     * @param array $data
     * @return array
     */
    private function regroupStatistic(array $data)
    {
        foreach ($this->getFiltersConfig() as $param) {
            $code   = $param['code'];
            
            $stat[] = [
                'title' => $param['title'],
                'type'  => $code,
                'lines' => [
                    [
                        'trclass' => 'stat-headers',
                        'items' => $this->getStatisticHeaders(),
                    ],
                    [
                        'trclass' => 'stat-values',
                        'items' => [
                            $data[SI::UNSUBSCRIBED] ?? 0,
                            $data[SI::SENT] ?? 0,
                            $data[SI::OPENED] ?? 0,
                            $data[SI::TRANSITED] ?? 0,
                            $this->ctr($data[SI::SENT] ?? 0, $data[SI::OPENED] ?? 0) . '%',
                            $this->price->convertAndFormat($data[SI::SALES . '_' . $code] ?? 0, false, 0),
                            $this->price->convertAndFormat($data[SI::SALES] ?? 0, false, 0)
                        ]
                    ],
                    [
                        'trclass' => 'stat-notes',
                        'items' => [
                            __('<b>+%1</b> last %2', $data[SI::UNSUBSCRIBED . '_' . $code] ?? 0, $param['period']),
                            __('<b>+%1</b> last %2', $data[SI::SENT . '_' . $code] ?? 0, $param['period']),
                            __('<b>+%1</b> last %2', $data[SI::OPENED . '_' . $code] ?? 0, $param['period']),
                            __('<b>+%1</b> last %2', $data[SI::TRANSITED . '_' . $code] ?? 0, $param['period']),
                            __('last %1', $param['period']),
                            __('last %1', $param['period']),
                            '',
                        ]
                    ],

                ],
            ];
        }
        
        return $stat;
    }
    
    /**
     * @return array
     */
    private function getStatisticHeaders()
    {
        return [__('Unsub'), __('Sent'), __('Opened'), __('Clicks'), __('CTR'), __('Sales'), __('Lifetime Sales')];
    }
    
    /**
     * @return array
     */
    private function getFiltersConfig()
    {
        return [
            [
                'title'     => __('Week'),
                'code'      => SI::WEEK,
                'period'    => '7D'
            ],
            [
                'title'     => __('Month'),
                'code'      => SI::MONTH,
                'period'    => '30D'
            ],
            [
                'title'     => __('6 Months'),
                'code'      => SI::HALF_YEAR,
                'period'    => '6M'
            ],
            [
                'title'     => __('Year'),
                'code'      => SI::YEAR,
                'period'    => '1Y'
            ],
        ];
    }
}
