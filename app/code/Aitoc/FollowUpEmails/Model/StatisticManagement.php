<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Model;

use Aitoc\FollowUpEmails\Api\Data\EmailAttributeInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailAttributeSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Email\StatusInterface;
use Aitoc\FollowUpEmails\Api\Data\StatisticInterface;
use Aitoc\FollowUpEmails\Api\Data\StatisticSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\EventManagementInterface;
use Aitoc\FollowUpEmails\Api\Helper\EventEmailsGeneratorHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailAttributeTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\StatisticTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\UnsubscribedEmailAddressTableInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\Statistic\Collection as StatisticsCollection;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class StatisticManagement
 */
class StatisticManagement
{
    const WEEK_PERIOD = 7;
    const MONTH_PERIOD = 30;
    const HALF_YEAR_PERIOD = 182;
    const YEAR_PERIOD = 365;

    /**
     * @var SearchCriteriaBuilderInterface
     */
    protected $searchCriteriaBuilderHelper;

    /**
     * @var EmailRepository
     */
    private $emailRepository;

    /**
     * @var CampaignStepRepository
     */
    private $campaignStepsRepository;

    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    /**
     * @var StatisticFactory
     */
    private $statisticFactory;

    /**
     * @var StatisticRepository
     */
    private $statisticsRepository;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var UnsubscribedEmailAddressRepository
     */
    private $unsubscribeRepository;

    /**
     * @var EmailAttributeRepository
     */
    private $emailAttributesRepository;

    /**
     * @var EventManagementInterface
     */
    private $eventManagement;

    /**
     * @var StatisticsCollection
     */
    private $statisticsCollection;

    /**
     * @param EmailRepository $emailRepository
     * @param SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper
     * @param CampaignStepRepository $campaignStepsRepository
     * @param CampaignRepository $campaignRepository
     * @param StatisticFactory $statisticFactory
     * @param DateTime $date
     * @param StatisticRepository $statisticsRepository
     * @param UnsubscribedEmailAddressRepository $unsubscribeRepository
     * @param EmailAttributeRepository $emailAttributesRepository
     * @param EventManagementInterface $eventManagement
     * @param StatisticsCollection $statisticsCollection
     */
    public function __construct(
        EmailRepository $emailRepository,
        SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper,
        CampaignStepRepository $campaignStepsRepository,
        CampaignRepository $campaignRepository,
        StatisticFactory $statisticFactory,
        DateTime $date,
        StatisticRepository $statisticsRepository,
        UnsubscribedEmailAddressRepository $unsubscribeRepository,
        EmailAttributeRepository $emailAttributesRepository,
        EventManagementInterface $eventManagement,
        StatisticsCollection $statisticsCollection
    ) {
        $this->emailRepository = $emailRepository;
        $this->searchCriteriaBuilderHelper = $searchCriteriaBuilderHelper;
        $this->campaignStepsRepository = $campaignStepsRepository;
        $this->campaignRepository = $campaignRepository;
        $this->statisticFactory = $statisticFactory;
        $this->date = $date;
        $this->statisticsRepository = $statisticsRepository;
        $this->unsubscribeRepository = $unsubscribeRepository;
        $this->emailAttributesRepository = $emailAttributesRepository;
        $this->eventManagement = $eventManagement;
        $this->statisticsCollection = $statisticsCollection;
    }

    /**
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function updateStatistic()
    {
        $this->zeroingData();

        $currentDateTime = $this->date->gmtDate();
        $weekPeriod = $this->datetimeStringForDaysAgo($currentDateTime, self::WEEK_PERIOD);
        $monthPeriod = $this->datetimeStringForDaysAgo($currentDateTime, self::MONTH_PERIOD);
        $halfYearPeriod = $this->datetimeStringForDaysAgo($currentDateTime, self::HALF_YEAR_PERIOD);
        $yearPeriod = $this->datetimeStringForDaysAgo($currentDateTime, self::YEAR_PERIOD);

        $sentEmails = $this->getSentEmails();

        foreach ($sentEmails as $email) {
            $emailId = $email->getEntityId();

            $campaignStepId = $email->getCampaignStepId();
            $campaignStep = $this->getCampaignStepById($campaignStepId);

            $campaignId = $campaignStep->getCampaignId();
            $campaign = $this->getCampaignById($campaignId);

            $eventCode = $campaign->getEventCode();

            $isEventEnabled = $this->isEventEnabled($eventCode);

            if (!$isEventEnabled) {
                continue;
            }

            $eventEmailGeneratorHelper = $this->getEventEmailGeneratorHelperByEventCode($eventCode);

            if (!$eventEmailGeneratorHelper) {
                continue;
            }

            $emailAttributesValues = $this->getEmailAttributesValues($emailId);
            $entityStatisticData = $eventEmailGeneratorHelper
                ->getEntityStatisticData($campaignStep, $emailAttributesValues);

            $emailSentAt = $email->getSentAt();
            $emailOpenAt = $email->getOpenedAt();
            $emailTransitedAt = $email->getTransitedAt();

            $unsubscribeItems = $this->getUnsubscribbed($eventCode, $emailId);

            $emailData = $this->getEmailStatisticData(
                $emailSentAt,
                $emailOpenAt,
                $emailTransitedAt,
                $weekPeriod,
                $monthPeriod,
                $halfYearPeriod,
                $yearPeriod,
                $unsubscribeItems,
                $entityStatisticData
            );

            $this->addStatistic($campaignId, $campaignStepId, $emailData, $currentDateTime, $eventCode);
        }
    }

    /**
     * Zeroing exist statistic data
     * @throws CouldNotSaveException
     */
    public function zeroingData()
    {
        $statisticItems = $this->getAllStatisticsItems();

        /** @var StatisticInterface $statisticItem */
        foreach ($statisticItems as $statisticItem) {
            $statisticItem->setValue(0);
            $this->saveStatisticItem($statisticItem);
        }
    }

    /**
     * @return StatisticInterface[]
     */
    private function getAllStatisticsItems()
    {
        $searchCriteria = $this->createSearchCriteria([]);
        $searchResults = $this->getStatisticSearchResults($searchCriteria);

        return $searchResults->getItems();
    }

    /**
     * @param array $filters
     * @return SearchCriteria
     */
    private function createSearchCriteria($filters)
    {
        return $this->searchCriteriaBuilderHelper->createSearchCriteria($filters);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return StatisticSearchResultsInterface
     */
    private function getStatisticSearchResults(SearchCriteria $searchCriteria)
    {
        return $this->statisticsRepository->getList($searchCriteria);
    }

    /**
     * @param StatisticInterface $statistic
     * @throws CouldNotSaveException
     */
    private function saveStatisticItem(StatisticInterface $statistic)
    {
        $this->statisticsRepository->save($statistic);
    }

    /**
     * @param string $currentDateTimeString
     * @param int $days
     * @return false|string
     */
    private function datetimeStringForDaysAgo($currentDateTimeString, $days)
    {
        return date('Y-m-d H:i:s', $this->timestampForDaysAgo($currentDateTimeString, $days));
    }

    /**
     * @param string $currentDateTimeString
     * @param int $days
     * @return false|int
     */
    private function timestampForDaysAgo($currentDateTimeString, $days)
    {
        return strtotime("{$currentDateTimeString} - {$days} days");
    }

    /**
     * @return EmailInterface[]
     */
    private function getSentEmails()
    {
        return $this->getEmailsByStatus(StatusInterface::STATUS_SENT);
    }

    /**
     * @param int $emailStatus
     * @return EmailInterface[]
     */
    private function getEmailsByStatus($emailStatus)
    {
        $filters = [
            [EmailTableInterface::COLUMN_NAME_STATUS, $emailStatus]
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $emailsList = $this->getEmailSearchResults($searchCriteria);

        return $emailsList->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return EmailSearchResultsInterface
     */
    private function getEmailSearchResults(SearchCriteria $searchCriteria)
    {
        return $this->emailRepository->getList($searchCriteria);
    }

    /**
     * @param int $campaignStepId
     * @return CampaignStep
     * @throws NoSuchEntityException
     */
    private function getCampaignStepById($campaignStepId)
    {
        return $this->campaignStepsRepository->get($campaignStepId);
    }

    /**
     * @param int $campaignId
     * @return Campaign
     * @throws NoSuchEntityException
     */
    private function getCampaignById($campaignId)
    {
        return $this->campaignRepository->get($campaignId);
    }

    /**
     * @param string $eventCode
     * @return bool
     */
    private function isEventEnabled($eventCode)
    {
        return $this->eventManagement->isEventEnabled($eventCode);
    }

    /**
     * @param $eventCode
     * @return EventEmailsGeneratorHelperInterface
     */
    private function getEventEmailGeneratorHelperByEventCode($eventCode)
    {
        return $this->eventManagement->getEventEmailGeneratorHelperByEventCode($eventCode);
    }

    /**
     * @param int $emailId
     * @return array
     */
    private function getEmailAttributesValues($emailId)
    {
        $emailAttributes = $this->getEmailAttributes($emailId);

        $emailAttributesValues = [];

        foreach ($emailAttributes as $emailAttribute) {
            $attributeCode = $emailAttribute->getAttributeCode();
            $attributeValue = $emailAttribute->getValue();
            $emailAttributesValues[$attributeCode] = $attributeValue;
        }

        return $emailAttributesValues;
    }

    /**
     * @param $emailId
     * @return EmailAttributeInterface[]
     */
    private function getEmailAttributes($emailId)
    {
        $filters = [
            [EmailAttributeTableInterface::COLUMN_NAME_EMAIL_ID, $emailId]
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getEmailAttributesBySearchCriteria($searchCriteria);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return EmailAttributeInterface[]
     */
    private function getEmailAttributesBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $emailAttributesList = $this->getEmailAttributeSearchResults($searchCriteria);

        return $emailAttributesList->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return EmailAttributeSearchResultsInterface
     */
    private function getEmailAttributeSearchResults(SearchCriteria $searchCriteria)
    {
        return $this->emailAttributesRepository->getList($searchCriteria);
    }

    /**
     * @param string $eventCode
     * @param int $emailId
     * @return UnsubscribedEmailAddressInterface[]
     */
    private function getUnsubscribbed($eventCode, $emailId)
    {
        $filters = [
            [UnsubscribedEmailAddressTableInterface::COLUMN_NAME_EMAIL_ID, $emailId],
            [UnsubscribedEmailAddressTableInterface::COLUMN_NAME_EVENT_CODE, $eventCode],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $unsubscribeList = $this->getUnsubscribeSearchResults($searchCriteria);

        return $unsubscribeList->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return UnsubscribedEmailAddressSearchResultsInterface
     */
    private function getUnsubscribeSearchResults(SearchCriteria $searchCriteria)
    {
        return $this->unsubscribeRepository->getList($searchCriteria);
    }

    /**
     * @param string $emailSent
     * @param string $emailOpen
     * @param string $emailTransited
     * @param string $weekPeriod
     * @param string $monthPeriod
     * @param string $halfYearPeriod
     * @param string $yearPeriod
     * @param array $unsubscribeItems
     * @param array $entityStatisticData
     * @return array
     */
    public function getEmailStatisticData($emailSent, $emailOpen, $emailTransited, $weekPeriod, $monthPeriod, $halfYearPeriod, $yearPeriod, $unsubscribeItems, $entityStatisticData)
    {
        $sent = $emailSent ? 1 : 0;
        $opened = $emailOpen ? 1 : 0;
        $transited = $emailTransited ? 1 : 0;
        $unsubscribed = $unsubscribeItems ? 1 : 0;

        $sentWeek = $emailSent >= $weekPeriod ? 1 : 0;
        $openWeek = $emailOpen >= $weekPeriod ? 1 : 0;
        $transitedWeek = $emailTransited >= $weekPeriod ? 1 : 0;

        $sentMonth = $emailSent >= $monthPeriod ? 1 : 0;
        $openMonth = $emailOpen >= $monthPeriod ? 1 : 0;
        $transitedMonth = $emailTransited >= $monthPeriod ? 1 : 0;

        $sentHalfYear = $emailSent >= $halfYearPeriod ? 1 : 0;
        $openHalfYear = $emailOpen >= $halfYearPeriod ? 1 : 0;
        $transitedHalfYear = $emailTransited >= $halfYearPeriod ? 1 : 0;

        $sentYear = $emailSent >= $yearPeriod ? 1 : 0;
        $openYear = $emailOpen >= $yearPeriod ? 1 : 0;
        $transitedYear = $emailTransited >= $yearPeriod ? 1 : 0;

        $emailData = [
            StatisticInterface::SENT => $sent,
            StatisticInterface::SENT . '_' . StatisticInterface::WEEK => $sentWeek,
            StatisticInterface::SENT . '_' . StatisticInterface::MONTH => $sentMonth,
            StatisticInterface::SENT . '_' . StatisticInterface::HALF_YEAR => $sentHalfYear,
            StatisticInterface::SENT . '_' . StatisticInterface::YEAR => $sentYear,
            StatisticInterface::OPENED => $opened,
            StatisticInterface::OPENED . '_' . StatisticInterface::WEEK => $openWeek,
            StatisticInterface::OPENED . '_' . StatisticInterface::MONTH => $openMonth,
            StatisticInterface::OPENED . '_' . StatisticInterface::HALF_YEAR => $openHalfYear,
            StatisticInterface::OPENED . '_' . StatisticInterface::YEAR => $openYear,
            StatisticInterface::TRANSITED => $transited,
            StatisticInterface::TRANSITED . '_' . StatisticInterface::WEEK => $transitedWeek,
            StatisticInterface::TRANSITED . '_' . StatisticInterface::MONTH => $transitedMonth,
            StatisticInterface::TRANSITED . '_' . StatisticInterface::HALF_YEAR => $transitedHalfYear,
            StatisticInterface::TRANSITED . '_' . StatisticInterface::YEAR => $transitedYear,
            StatisticInterface::UNSUBSCRIBED => $unsubscribed
        ];

        foreach ($unsubscribeItems as $unsubscribeItem) {
            $createdAt = $unsubscribeItem->getCreatedAt();
            $emailData[StatisticInterface::UNSUBSCRIBED . '_' . StatisticInterface::WEEK] = $createdAt >= $weekPeriod ? 1 : 0;
            $emailData[StatisticInterface::UNSUBSCRIBED . '_' . StatisticInterface::MONTH] = $createdAt >= $monthPeriod ? 1 : 0;
            $emailData[StatisticInterface::UNSUBSCRIBED . '_' . StatisticInterface::HALF_YEAR] = $createdAt >= $halfYearPeriod ? 1 : 0;
            $emailData[StatisticInterface::UNSUBSCRIBED . '_' . StatisticInterface::YEAR] = $createdAt >= $yearPeriod ? 1 : 0;
        }

        if ($entityStatisticData) {
            $createdAt = $entityStatisticData['createdAt'];
            $grandTotal = $entityStatisticData['grandTotal'];
            $emailData[StatisticInterface::SALES] = $grandTotal;
            $emailData[StatisticInterface::SALES . '_' . StatisticInterface::WEEK] = $createdAt >= $weekPeriod ? $grandTotal : 0;
            $emailData[StatisticInterface::SALES . '_' . StatisticInterface::MONTH] = $createdAt >= $monthPeriod ? $grandTotal : 0;
            $emailData[StatisticInterface::SALES . '_' . StatisticInterface::HALF_YEAR] = $createdAt >= $halfYearPeriod ? $grandTotal : 0;
            $emailData[StatisticInterface::SALES . '_' . StatisticInterface::YEAR] = $createdAt >= $yearPeriod ? $grandTotal : 0;
        }
        return $emailData;
    }

    /**
     * @param int $campaignId
     * @param int $campaignStepId
     * @param array $emailData
     * @param string $currentDateTime
     * @param string $eventCode
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function addStatistic($campaignId, $campaignStepId, $emailData, $currentDateTime, $eventCode)
    {
        foreach ($emailData as $key => $value) {
            $filters = [
                [StatisticTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID, $campaignStepId],
                [StatisticTableInterface::COLUMN_NAME_KEY, $key]
            ];

            $searchCriteria = $this->createSearchCriteria($filters);

            $statisticsList = $this->getStatisticSearchResults($searchCriteria);
            $statisticsItems = $statisticsList->getItems();

            if ($statisticsItems) {
                foreach ($statisticsItems as $item) {
                    $statisticId = $item->getEntityId();
                    $statisticModel = $this->getStatisticById($statisticId);
                    $existValue = $item->getValue();
                    $totalValue = $existValue + $value;
                    $this->saveStatistic($statisticModel, $campaignId, $campaignStepId, $key, $totalValue, $currentDateTime, $eventCode);
                }
            } else {
                $statisticModel = $this->createStatistic();
                $this->saveStatistic($statisticModel, $campaignId, $campaignStepId, $key, $value, $currentDateTime, $eventCode);
            }
        }
    }

    /**
     * @param int $statisticId
     * @return Statistic
     * @throws NoSuchEntityException
     */
    private function getStatisticById($statisticId)
    {
        return $this->statisticsRepository->get($statisticId);
    }

    /**
     * @param Statistic $statisticModel
     * @param int $campaignId
     * @param int $campaignStepId
     * @param string $key
     * @param int $value
     * @param string $eventCode
     * @param string $currentDateTime
     * @throws CouldNotSaveException
     */
    public function saveStatistic($statisticModel, $campaignId, $campaignStepId, $key, $value, $currentDateTime, $eventCode)
    {
        $statisticModel
            ->setCampaignId($campaignId)
            ->setCampaignStepId($campaignStepId)
            ->setKey($key)
            ->setValue($value)
            ->setUpdatedAt($currentDateTime)
            ->setEventCode($eventCode)
        ;

        $this->saveStatisticItem($statisticModel);
    }

    /**
     * @return Statistic
     */
    private function createStatistic()
    {
        return $this->statisticFactory->create();
    }

    /**
     * @param string $eventCode
     * @return array
     */
    public function getStatisticByEvent($eventCode)
    {
        $filters = [
            [StatisticTableInterface::COLUMN_NAME_EVENT_CODE, $eventCode],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $statisticData = $this->filteringStatisticData($searchCriteria);

        return $statisticData;
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return array
     */
    public function filteringStatisticData(SearchCriteria $searchCriteria)
    {
        $statisticsList = $this->getStatisticSearchResults($searchCriteria);
        $statisticData = [];
        foreach ($statisticsList->getItems() as $statistic) {
            $key = $statistic->getKey();
            $value = $statistic->getValue();
            if (array_key_exists($key, $statisticData)) {
                $statisticData[$key] += $value;
            } else {
                $statisticData[$key] = $value;
            }
        }
        return $statisticData;
    }

    /**
     * @param int $campaignId
     * @return array
     */
    public function getStatisticByCampaign($campaignId)
    {
        $filters = [
            [StatisticTableInterface::COLUMN_NAME_CAMPAIGN_ID, $campaignId],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $statisticData = $this->filteringStatisticData($searchCriteria);

        return $statisticData;
    }

    /**
     * @param int $campaignStepId
     * @return array
     */
    public function getStatisticByCampaignStep($campaignStepId)
    {
        $filters = [
            [StatisticTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID, $campaignStepId],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $statisticData = $this->filteringStatisticData($searchCriteria);

        return $statisticData;
    }

    /**
     * @param string $eventCode
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function resetStatisticByEvent($eventCode)
    {
        $filters = [
            [StatisticTableInterface::COLUMN_NAME_EVENT_CODE, $eventCode],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $this->resetStatisticData($searchCriteria, $eventCode);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @param string $eventCode
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function resetStatisticData(SearchCriteria $searchCriteria, $eventCode)
    {
        $statisticsList = $this->getStatisticSearchResults($searchCriteria);

        foreach ($statisticsList->getItems() as $statisticItem) {
            $campaignStepId = $statisticItem->getCampaignStepId();
            $this->deleteStatistic($statisticItem);
            $filters = [
                [EmailTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID, $campaignStepId]
            ];
            $searchCriteria = $this->createSearchCriteria($filters);

            $emailList = $this->getEmailSearchResults($searchCriteria);

            foreach ($emailList->getItems() as $email) {
                $emailId = $email->getEntityId();
                $email->setSentAt(0);
                $email->setOpenedAt(0);
                $email->setTransitedAt(0);
                $this->saveEmail($email);

                $filters = [
                    [UnsubscribedEmailAddressTableInterface::COLUMN_NAME_EMAIL_ID, $emailId],
                    [UnsubscribedEmailAddressTableInterface::COLUMN_NAME_EVENT_CODE, $eventCode],
                ];

                $searchCriteria = $this->createSearchCriteria($filters);
                $unsubscribeList = $this->getUnsubscribeSearchResults($searchCriteria);

                foreach ($unsubscribeList->getItems() as $item) {
                    $item->setEmailId(0);
                    $this->saveUnsubscribeListItem($item);
                }
            }
        }
    }

    /**
     * @param StatisticInterface $statistic
     * @return bool
     * @throws CouldNotDeleteException
     */
    private function deleteStatistic(StatisticInterface $statistic)
    {
        return $this->statisticsRepository->delete($statistic);
    }

    /**
     * @param EmailInterface $email
     * @return EmailInterface
     * @throws CouldNotSaveException
     */
    private function saveEmail(EmailInterface $email)
    {
        return $this->emailRepository->save($email);
    }

    /**
     * @param UnsubscribedEmailAddressInterface $item
     * @return UnsubscribedEmailAddressInterface
     * @throws CouldNotSaveException
     */
    private function saveUnsubscribeListItem(UnsubscribedEmailAddressInterface $item)
    {
        return $this->unsubscribeRepository->save($item);
    }

    /**
     * @param int $campaignId
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function resetStatisticByCampaign($campaignId)
    {
        $campaign = $this->getCampaignById($campaignId);
        $eventCode = $campaign->getEventCode();

        $filters = [
            [StatisticTableInterface::COLUMN_NAME_CAMPAIGN_ID, $campaignId],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        $this->resetStatisticData($searchCriteria, $eventCode);
    }

    /**
     * @param int $campaignStepId
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function resetStatisticByCampaignStep($campaignStepId)
    {
        $campaignStep = $this->getCampaignStepById($campaignStepId);
        $campaignId = $campaignStep->getCampaignId();
        $campaign = $this->getCampaignById($campaignId);
        $eventCode = $campaign->getEventCode();

        $filters = [
            [StatisticTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID, $campaignStepId],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $this->resetStatisticData($searchCriteria, $eventCode);
    }
}
