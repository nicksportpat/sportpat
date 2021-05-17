<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Helper\EmailGenerator;

use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Helper\EmailInterface as EmailHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface;
use Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper\Order\CanSendByStatus\WithConfigurableStatuses as OrderCanSendByConfigurableStatusesEventEmailsGeneratorHelper;
use Aitoc\FollowUpEmails\Helper\ProductsProvider\BelongsTo\OrderId as BelongsToOrderIdProductsProvider;
use Aitoc\ReviewBooster\Api\Service\ConfigProviderInterface as ConfigHelperInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTimeHelper;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class ReviewBooster
 */
class ReviewBooster extends OrderCanSendByConfigurableStatusesEventEmailsGeneratorHelper
{
    const ATTRIBUTE_CODE_ORDER_ID = 'order_id';
    const ATTRIBUTE_CODE_ORDER_STATUS = 'order_status';
    const MIN_PRODUCTS_COUNT = 1;
    const MAX_ORDER_PRODUCTS = 6;

    /**
     * @var SearchCriteriaBuilderInterface
     */
    private $searchCriteriaBuilderHelper;

    /**
     * @var ConfigHelperInterface
     */
    private $configHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var BelongsToOrderIdProductsProvider
     */
    private $belongsToOrderIdProductsProvider;

    /**
     * @param EmailHelperInterface $emailHelper
     * @param DateTimeHelper $dateTimeHelper
     * @param SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper
     * @param ConfigHelperInterface $configHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param BelongsToOrderIdProductsProvider $belongsToOrderIdProductsProvider
     */
    public function __construct(
        EmailHelperInterface $emailHelper,
        DateTimeHelper $dateTimeHelper,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper,
        ConfigHelperInterface $configHelper,
        CustomerRepositoryInterface $customerRepository,
        BelongsToOrderIdProductsProvider $belongsToOrderIdProductsProvider
    ) {
        parent::__construct($emailHelper, $dateTimeHelper, $orderRepository);

        $this->searchCriteriaBuilderHelper = $searchCriteriaBuilderHelper;
        $this->configHelper = $configHelper;
        $this->customerRepository = $customerRepository;
        $this->belongsToOrderIdProductsProvider = $belongsToOrderIdProductsProvider;
    }

    /**
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param array $processedEntitiesIds
     * @return array
     */
    public function getUnprocessedEntities(
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        $processedEntitiesIds
    ) {
        $campaignCustomerGroupIds = $campaign->getCustomerGroupIds();
        $campaignStoreIds = $campaign->getStoreIds();
        $allowedStatuses  = $this->getAllowedOrderStatuses($campaignStep);
        $toDate = $this->getToData($campaignStep);

        $filters = [
            ['main_table.customer_group_id', $campaignCustomerGroupIds, 'in'],
            ['main_table.store_id', $campaignStoreIds, 'in'],
            ['main_table.status', $allowedStatuses, 'in'],
            ['main_table.customer_email', true, 'notnull'],
            ['main_table.updated_at', $toDate, 'lt'],
        ];

        if ($processedEntitiesIds) {
            $filters[] = ['main_table.entity_id', $processedEntitiesIds, 'nin'];
        }

        $sortOrders = ['updated_at' => SortOrder::SORT_ASC];

        $searchCriteria = $this->createSearchCriteria($filters, $sortOrders);

        return $this->getOrdersBySearchCriteria($searchCriteria);
    }

    /**
     * Get list of allowed statuses to send reminders
     *
     * @param CampaignStepInterface $campaignStep
     * @return array
     */
    protected function getAllowedOrderStatuses(CampaignStepInterface $campaignStep)
    {
        $orderStatuses = $this->getConfigOrderStatuses();
        $statuses = explode(',', $orderStatuses);
        return $statuses;
    }

    /**
     * @return string
     */
    private function getConfigOrderStatuses()
    {
        return $this->configHelper->getOrderStatuses();
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @return string
     */
    private function getToData(CampaignStepInterface $campaignStep)
    {
        $delayPeriod = $campaignStep->getDelayPeriod();
        $delayUnit = $campaignStep->getDelayUnit();

        $currentDateTimeString = $this->getCurrentDataTimeString();

        return $this->getToDataString($currentDateTimeString, $delayPeriod, $delayUnit);
    }

    /**
     * @return string
     */
    private function getCurrentDataTimeString()
    {
        return $this->dateTimeHelper->gmtDate();
    }

    /**
     * @param string $currentDateTimeString
     * @param int $delayPeriod
     * @param string $delayUnit
     * @return string
     */
    private function getToDataString($currentDateTimeString, $delayPeriod, $delayUnit)
    {
        return date('Y:m:d H:i:s', strtotime("{$currentDateTimeString} - {$delayPeriod} {$delayUnit}"));
    }

    /**
     * @param array $filters
     * @param array $orders
     * @return SearchCriteria
     */
    private function createSearchCriteria($filters = [], $orders = [])
    {
        return $this->searchCriteriaBuilderHelper->createSearchCriteria($filters, $orders);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return OrderInterface[]
     */
    private function getOrdersBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $orderSearchResult = $this->getOrderSearchResult($searchCriteria);

        return $orderSearchResult->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return OrderSearchResultInterface
     */
    private function getOrderSearchResult(SearchCriteria $searchCriteria)
    {
        return $this->orderRepository->getList($searchCriteria);
    }

    /**
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param EmailInterface $email
     * @param array $emailAttributes
     * @return array
     */
    public function getModuleData(
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        EmailInterface $email,
        $emailAttributes
    ) {
        $products = $this->getLimitedProductsByEmailAttributes($emailAttributes);

        return [
            'products' => $products,
        ];
    }

    /**
     * @param array $emailAttributes
     * @return array
     */
    private function getLimitedProductsByEmailAttributes($emailAttributes)
    {
        $orderId = $this->getOrderIdByEmailAttributes($emailAttributes);
        $maxCount = $this->getMaxProductsCount();

        return $this->getBelongsToOrderIdProducts($orderId, $maxCount);

    }

    /**
     * @param array $emailAttributes
     * @return int
     */
    private function getOrderIdByEmailAttributes($emailAttributes)
    {
        return (int) $emailAttributes[self::ATTRIBUTE_CODE_ORDER_ID];
    }

    /**
     * @return int
     */
    private function getMaxProductsCount()
    {
        $configProductsCount = $this->getProductsCountByConfig();

        return max($configProductsCount, self::MIN_PRODUCTS_COUNT);
    }

    /**
     * @return int
     */
    private function getProductsCountByConfig()
    {
        return self::MAX_ORDER_PRODUCTS;
    }

    /**
     * @param int $orderId
     * @param int $maxCount
     * @return ProductInterface[]
     */
    private function getBelongsToOrderIdProducts($orderId, $maxCount)
    {
        return $this->belongsToOrderIdProductsProvider->getProducts($orderId, $maxCount);
    }

    /**
     * @inheritDoc
     */
    public function getEntityStatisticData(CampaignStepInterface $campaignStep, $emailAttributes)
    {
        return false;
    }
}
