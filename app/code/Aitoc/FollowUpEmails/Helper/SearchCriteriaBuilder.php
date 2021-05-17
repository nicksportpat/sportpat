<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper;

use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder as MagentoSearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\FilterGroup;

/**
 * Class SearchCriteriaBuilder
 */
class SearchCriteriaBuilder implements SearchCriteriaBuilderInterface
{
    /**
     * @var MagentoSearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * SearchCriteriaBuilder constructor.
     * @param MagentoSearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        MagentoSearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * @param array $filters
     * @param array $sortOrders
     * @return SearchCriteria
     */
    public function createSearchCriteria($filters = [], $sortOrders = [])
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;

        if ($filters) {
            $this->addSearchCriteriaFilters($searchCriteriaBuilder, $filters);
        }

        if ($sortOrders) {
            $this->addSortOrders($searchCriteriaBuilder, $sortOrders);
        }

        return $this->searchCriteriaBuilder->create();
    }

    /**
     * @param MagentoSearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $filters
     */
    private function addSearchCriteriaFilters(MagentoSearchCriteriaBuilder $searchCriteriaBuilder, $filters)
    {
        foreach ($filters as $filter) {
            !$this->isFilterGroup($filter)
                ? $this->addFilter($searchCriteriaBuilder, $filter)
                : $this->addFilterGroup($searchCriteriaBuilder, $filter)
            ;
        }
    }

    /**
     * @param mixed $filter
     * @return bool
     */
    private function isFilterGroup($filter)
    {
        $firstItem = reset($filter);

        return is_array($firstItem);
    }

    /**
     * @param MagentoSearchCriteriaBuilder $searchCriteriaBuilder
     * @param $filterGroupArray
     * @return MagentoSearchCriteriaBuilder
     */
    private function addFilterGroup(MagentoSearchCriteriaBuilder $searchCriteriaBuilder, $filterGroupArray)
    {
        $createFilterGroup = $this->createFilterGroup($filterGroupArray);

        return $searchCriteriaBuilder->setFilterGroups([$createFilterGroup]);
    }

    /**
     * @param $filterGroupArray
     * @return AbstractSimpleObject|FilterGroup
     */
    private function createFilterGroup($filterGroupArray)
    {
        $filters = $this->createFilters($filterGroupArray);
        $filterGroupBuilder = $this->filterGroupBuilder;

        return $filterGroupBuilder->setFilters($filters)->create();
    }

    /**
     * @param array $filterGroupArray
     * @return array
     */
    private function createFilters($filterGroupArray)
    {
        $filters = [];

        foreach ($filterGroupArray as $filterArray) {
            $filters[] = $this->createFilter($filterArray);
        }

        return $filters;
    }

    /**
     * @param array $filterArray
     * @return Filter
     */
    private function createFilter($filterArray)
    {
        $filterBuilder = $this->filterBuilder;

        $filterField = $filterArray[0];
        $filterValue = $filterArray[1];
        $filterCondition = isset($filterArray[2]) ? $filterArray[2] : 'eq';

        $filterBuilder
            ->setField($filterField)
            ->setValue($filterValue)
            ->setConditionType($filterCondition)
        ;

        return $filterBuilder->create();
    }

    /**
     * @param MagentoSearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $filter
     */
    private function addFilter (MagentoSearchCriteriaBuilder $searchCriteriaBuilder, $filter)
    {
        $searchCriteriaBuilder->addFilter(
            $filter[0],
            $filter[1],
            isset($filter[2]) ? $filter[2] : 'eq'
        );
    }

    /**
     * @param MagentoSearchCriteriaBuilder $searCriteriaBuilder
     * @param array $sortOrders
     */
    private function addSortOrders(MagentoSearchCriteriaBuilder $searCriteriaBuilder, $sortOrders)
    {
        foreach ($sortOrders as $field => $direction) {
            $sortOrder = $this->createSortOrder($field, $direction);
            $searCriteriaBuilder->addSortOrder($sortOrder);
        }
    }

    /**
     * @param string $field
     * @param string $direction
     * @return SortOrder
     */
    private function createSortOrder($field, $direction)
    {
        return $this->sortOrderBuilder
            ->setField($field)
            ->setDirection($direction)
            ->create();
    }
}