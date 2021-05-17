<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Model\Data\Event;

use Aitoc\FollowUpEmails\Api\EventManagementInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignTableInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\Campaign\CollectionFactory as CampaignsCollectionFactory;
use Aitoc\FollowUpEmails\Model\StatisticManagement;
use Exception;
use Magento\Framework\Data\Collection as MagentoCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;

/**
 * Class Collection
 */
class Collection extends MagentoCollection
{
    const FIELD_CAMPAIGN_COUNT = 'campaign_count';
    
    /**
     * Filter rendering helper variable
     *
     * @var int
     * @see Collection::$_filter
     * @see Collection::$_isFiltersRendered
     */
    protected $filterIncrement = 0;
    
    /**
     * @var EventManagementInterface
     */
    protected $eventManager;

    /**
     * @var StatisticManagement
     */
    protected $statisticManager;
    
    /**
     * @var CampaignsCollectionFactory
     */
    protected $campaignsCollectionFactory;
    
    /**
     * @param EntityFactoryInterface $entityFactory
     * @param EventManagementInterface $eventManager
     * @param StatisticManagement $statisticManager
     * @param CampaignsCollectionFactory $campaignsCollectionFactory
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        EventManagementInterface $eventManager,
        StatisticManagement $statisticManager,
        CampaignsCollectionFactory $campaignsCollectionFactory
    ) {
        parent::__construct($entityFactory);
        
        $this->eventManager = $eventManager;
        $this->statisticManager = $statisticManager;
        $this->campaignsCollectionFactory = $campaignsCollectionFactory;
    }

    /**
     * Lauch data collecting
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws Exception
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        
        $allEvents = $this->prepareData();
        
        // calculate totals
        $this->_totalRecords = count($allEvents);
        $this->_setIsLoaded();

        // paginate and add items
        $from = ($this->getCurPage() - 1) * $this->getPageSize();
        $to = $from + $this->getPageSize() - 1;
        $isPaginated = $this->getPageSize() > 0;

        $cnt = 0;
        foreach ($allEvents as $row) {
            $cnt++;
            if ($isPaginated && ($cnt < $from || $cnt > $to)) {
                continue;
            }
            $item = new $this->_itemObjectClass();
            $this->addItem($item->addData($row));
            if (!$item->hasId()) {
                $item->setId($cnt);
            }
        }

        return $this;
    }
    
    /**
     * Append extra data to collection
     *
     * @return array
     */
    private function prepareData()
    {
        $rows = $this->eventManager->getAllEvents() ?? [];

        $campaignCount = $this->collectCampaignCount();
        
        // collect data
        foreach ($rows as $index => $row) {
            if (empty($row['code'])) {
                continue;
            }
            
            $rows[$index]['campaigns'] = isset($campaignCount[$row['code']])
                ? $campaignCount[$row['code']]
                : 0;
            
            $rows[$index]['statistics'] = $this->statisticManager->getStatisticByEvent($row['code']);
        }

        // apply filters on generated data
        if (!empty($this->_filters)) {
            foreach ($rows as $key => $row) {
                if (!$this->_filterRow($row)) {
                    unset($rows[$key]);
                }
            }
        }

        // sort (keys are lost!)
        if (!empty($this->_orders)) {
            usort($rows, [$this, '_usort']);
        }
        
        return $rows;
    }

    /**
     * Callback for sorting items
     * Currently supports only sorting by one column
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    private function _usort($a, $b)
    {
        foreach ($this->_orders as $key => $direction) {
            $result = $a[$key] > $b[$key] ? 1 : ($a[$key] < $b[$key] ? -1 : 0);

            return self::SORT_ORDER_ASC === strtoupper($direction) ? $result : -$result;
        }
    }

    /**
     * Collect campaign count
     *
     * @return array
     */
    private function collectCampaignCount()
    {
        $campaigns = $this->campaignsCollectionFactory->create()
                ->addFieldToSelect(CampaignTableInterface::COLUMN_NAME_EVENT_CODE)
                ->addFieldToFilter(CampaignTableInterface::COLUMN_NAME_STATUS, 1);
        
        $campaigns->getSelect()
            ->columns('COUNT(*) AS ' . self::FIELD_CAMPAIGN_COUNT)
            ->group(CampaignTableInterface::COLUMN_NAME_EVENT_CODE);
        
        return array_column(
            $campaigns->getData(),
            self::FIELD_CAMPAIGN_COUNT,
            CampaignTableInterface::COLUMN_NAME_EVENT_CODE
        );
    }
    
    /**
     * The filters renderer and caller
     * Applies to each row, renders once.
     *
     * @param array $row
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    protected function _filterRow($row)
    {
        $result = false;
        
        for ($i = 0; $i < $this->filterIncrement; $i++) {
            $filter = $this->_filters[$i];
            $result |= $this->invokeFilter($filter['callback'], [$filter['field'], $filter['value'], $row]);
        }
        
        return $result;
    }
    
    /**
     * Invokes specified callback
     * Skips, if there is no filtered key in the row
     *
     * @param callback $callback
     * @param array $callbackParams
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function invokeFilter($callback, $callbackParams)
    {
        list($field, $value, $row) = $callbackParams;
        if (!array_key_exists($field, $row)) {
            return false;
        }
        return call_user_func_array($callback, $callbackParams);
    }
    
    /**
     * Fancy field filter
     *
     * @param string $field
     * @param mixed $cond
     * @param string $type 'and' | 'or'
     * @see Db::addFieldToFilter()
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function addFieldToFilter($field, $cond, $type = 'and')
    {
        // simply check whether equals
        if (!is_array($cond)) {
            return $this->addCallbackFilter($field, $cond, $type, [$this, 'filterCallbackEq']);
        }
        
        if (isset($cond['eq'])) {
            return $this->addCallbackFilter($field, $cond['eq'], $type, [$this, 'filterCallbackEq']);
        }
        
        return $this;
    }
    
    /**
     * Set a custom filter with callback
     * The callback must take 3 params:
     *     string $field       - field key,
     *     mixed  $filterValue - value to filter by,
     *     array  $row         - a generated row (before generating varien objects)
     *
     * @param string $field
     * @param mixed $value
     * @param string $type 'and'|'or'
     * @param callback $callback
     * @param bool $isInverted
     * @return $this
     */
    public function addCallbackFilter($field, $value, $type, $callback, $isInverted = false)
    {
        $this->_filters[$this->filterIncrement] = [
            'field' => $field,
            'value' => $value,
            'is_and' => 'and' === $type,
            'callback' => $callback,
            'is_inverted' => $isInverted,
        ];
        $this->filterIncrement++;
        return $this;
    }
    
    /**
     * Callback method for 'eq' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackEq($field, $filterValue, $row)
    {
        return $filterValue == $row[$field];
    }
}
