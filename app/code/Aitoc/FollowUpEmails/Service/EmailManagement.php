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

use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\CampaignStepRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailAttributeInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailAttributeSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterfaceFactory;
use Aitoc\FollowUpEmails\Api\Data\EmailSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Campaign\StatusInterface as CampaignStatusInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\CampaignStep\StatusInterface as CampaignStepStatusInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Email\StatusInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\EmailAttributeRepositoryInterface;
use Aitoc\FollowUpEmails\Api\EmailRepositoryInterface;
use Aitoc\FollowUpEmails\Api\EventManagementInterface;
use Aitoc\FollowUpEmails\Api\Helper\EmailInterface as EmailHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\EventEmailsGeneratorHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface;
use Aitoc\FollowUpEmails\Api\Service\CampaignStepInterface as CampaignStepServiceInterface;
use Aitoc\FollowUpEmails\Api\Service\CustomerGroupInterface as CustomerGroupServiceInterface;
use Aitoc\FollowUpEmails\Api\Service\Email\UnsubscribeCodeInterface as UnsubscribeCodeServiceInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignStepTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailAttributeTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\UnsubscribedEmailAddressTableInterface;
use Aitoc\FollowUpEmails\Api\UnsubscribedEmailAddressRepositoryInterface;
use Aitoc\FollowUpEmails\Helper\WebsiteInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\Area;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Api\Data\CouponInterfaceFactory;
use Magento\SalesRule\Api\Data\CouponSearchResultInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\Data\RuleInterfaceFactory;
use Magento\SalesRule\Helper\Coupon as CouponHelper;
use Magento\SalesRule\Model\Coupon\Massgenerator;
use Magento\SalesRule\Model\RuleRepository;
use Aitoc\FollowUpEmails\Ui\Component\Listing\Column\DiscountTypeOptions;
use Magento\Directory\Model\CurrencyFactory;

/**
 * Class EmailManagement
 */
class EmailManagement extends AbstractModel
{
    const CART_PRICE_RULE_NAME = 'Individual sales rule for %s';
    const CART_PRICE_RULE_COUPON_CODE_LENGTH = 12;

    const COUPON_MASS_GENERATOR_DATA_KEY_LENGTH = 'length';

    const ATTRIBUTE_CODE_RULE_ID = 'rule_id';

    const AITOC_FOLLOW_UP_IDENTIFIER = 'is_aitfollowup';

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var EmailInterfaceFactory
     */
    private $emailFactory;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignsRepository;

    /**
     * @var CampaignStepRepositoryInterface
     */
    private $campaignStepsRepository;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var EventManagementInterface
     */
    private $eventManagement;

    /**
     * @var EmailRepositoryInterface
     */
    private $emailsRepository;

    /**
     * @var UnsubscribedEmailAddressRepositoryInterface
     */
    private $unsubscribedListRepository;

    /**
     * @var EmailAttributeRepositoryInterface
     */
    private $emailAttributesRepository;

    /**
     * @var Massgenerator
     */
    private $couponMassGenerator;

    /**
     * @var CouponInterfaceFactory
     */
    private $couponFactory;

    /**
     * @var CouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var SearchCriteriaBuilderInterface
     */
    private $searchCriteriaBuilderHelper;

    /**
     * @var WebsiteInterface
     */
    private $websiteHelper;

    /**
     * @var EmailHelperInterface
     */
    private $emailHelper;

    /**
     * @var CustomerGroupServiceInterface
     */
    private $customerGroupService;

    /**
     * @var UnsubscribeCodeServiceInterface
     */
    private $unsubscribeCodeService;

    /**
     * @var CampaignStepServiceInterface
     */
    private $campaignStepService;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    public function __construct(
        Context $context,
        EventManagementInterface $eventManagement,
        CampaignRepositoryInterface $campaignsRepository,
        CampaignStepRepositoryInterface $campaignStepsRepository,
        EmailInterfaceFactory $emailFactory,
        EmailRepositoryInterface $emailsRepository,
        EmailAttributeRepositoryInterface $emailAttributesRepository,
        Registry $registry,
        DateTime $date,
        TransportBuilder $transportBuilder,
        SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper,
        UnsubscribedEmailAddressRepositoryInterface $unsubscribedListRepository,
        Massgenerator $couponMassGenerator,
        CouponInterfaceFactory $couponFactory,
        CouponRepositoryInterface $couponRepository,
        RuleInterfaceFactory $ruleFactory,
        RuleRepository $ruleRepository,
        WebsiteInterface $websiteHelper,
        EmailHelperInterface $emailHelper,
        CustomerGroupServiceInterface $customerGroupService,
        UnsubscribeCodeServiceInterface $unsubscribeCodeService,
        CampaignStepServiceInterface $campaignStepService,
        CurrencyFactory $currencyFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->date = $date;
        $this->emailFactory = $emailFactory;
        $this->campaignsRepository = $campaignsRepository;
        $this->campaignStepsRepository = $campaignStepsRepository;
        $this->transportBuilder = $transportBuilder;
        $this->searchCriteriaBuilderHelper = $searchCriteriaBuilderHelper;
        $this->eventManagement = $eventManagement;
        $this->emailsRepository = $emailsRepository;
        $this->unsubscribedListRepository = $unsubscribedListRepository;
        $this->emailAttributesRepository = $emailAttributesRepository;
        $this->couponMassGenerator = $couponMassGenerator;
        $this->couponFactory = $couponFactory;
        $this->couponRepository = $couponRepository;
        $this->ruleRepository = $ruleRepository;
        $this->websiteHelper = $websiteHelper;
        $this->ruleFactory = $ruleFactory;
        $this->emailHelper = $emailHelper;
        $this->customerGroupService = $customerGroupService;
        $this->unsubscribeCodeService = $unsubscribeCodeService;
        $this->campaignStepService = $campaignStepService;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * Prepare emails for generate
     *
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function generateEmails()
    {
        $eventCodes = $this->getActiveEventsCodes();
        $campaigns = $this->getEnabledCampaignsByEventsCodes($eventCodes);

        $this->generateCampaignsEmails($campaigns);
    }

    /**
     * @return array
     */
    private function getActiveEventsCodes()
    {
        return $this->eventManagement->getActiveEventsCodes();
    }

    /**
     * @param $eventsCodes
     * @return CampaignInterface[]
     */
    private function getEnabledCampaignsByEventsCodes($eventsCodes)
    {
        $filters = [
            [CampaignTableInterface::COLUMN_NAME_STATUS, CampaignStatusInterface::ENABLED],
            [CampaignTableInterface::COLUMN_NAME_EVENT_CODE, $eventsCodes, 'in']
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getCampaignsBySearchCriteria($searchCriteria);
    }

    /**
     * @param array $filters
     * @param array $sortOrders
     * @return SearchCriteria
     */
    private function createSearchCriteria($filters = [], $sortOrders = [])
    {
        return $this->searchCriteriaBuilderHelper->createSearchCriteria($filters, $sortOrders);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CampaignInterface[]
     */
    private function getCampaignsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $campaignSearchResult = $this->getCampaignSearchResultBySearchCriteria($searchCriteria);

        return $campaignSearchResult->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CampaignSearchResultsInterface
     */
    private function getCampaignSearchResultBySearchCriteria(SearchCriteria $searchCriteria)
    {
        return $this->campaignsRepository->getList($searchCriteria);
    }

    /**
     * @param $campaigns
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateCampaignsEmails($campaigns)
    {
        foreach ($campaigns as $campaign) {
            $this->generateCampaignEmails($campaign);
        }
    }

    /**
     * @param CampaignInterface $campaign
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateCampaignEmails(CampaignInterface $campaign) {
        $eventCode = $campaign->getEventCode();
        $eventEmailGeneratorHelper = $this->getEventEmailsGeneratorHelperByEventCode($eventCode);

        if (!$eventEmailGeneratorHelper) {
            return;
        }

        $campaignId = $campaign->getEntityId();
        $campaignSteps = $this->getEnabledCampaignStepsByCampaignId($campaignId);

        $this->generateCampaignStepsEmails($eventEmailGeneratorHelper, $campaign, $campaignSteps);
    }

    /**
     * @param string $eventCode
     * @return EventEmailsGeneratorHelperInterface
     */
    private function getEventEmailsGeneratorHelperByEventCode($eventCode)
    {
        return $this->eventManagement->getEventEmailGeneratorHelperByEventCode($eventCode);
    }

    /**
     * @param int $campaignId
     * @return CampaignStepInterface[]
     */
    private function getEnabledCampaignStepsByCampaignId($campaignId)
    {
        $filters = [
            [CampaignStepTableInterface::COLUMN_NAME_CAMPAIGN_ID, $campaignId],
            [CampaignStepTableInterface::COLUMN_NAME_STATUS, CampaignStepStatusInterface::ENABLED]
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getCampaignStepsBySearchCriteria($searchCriteria);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CampaignStepInterface[]
     */
    private function getCampaignStepsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $campaignStepSearchResults = $this->getCampaignStepSearchResultsBySearchCriteria($searchCriteria);

        return $campaignStepSearchResults->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CampaignStepSearchResultsInterface
     */
    private function getCampaignStepSearchResultsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        return $this->campaignStepsRepository->getList($searchCriteria);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface[] $campaignSteps
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateCampaignStepsEmails(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        $campaignSteps
    ) {
        foreach ($campaignSteps as $campaignStep) {
            $this->generateCampaignStepEmails($eventEmailsGeneratorHelper, $campaign, $campaignStep);
        }
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateCampaignStepEmails(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep
    ) {
        $unprocessedEntities = $this->getUnprocessedEntities(
            $eventEmailsGeneratorHelper,
            $campaign,
            $campaignStep
        );

        if (!$unprocessedEntities) {
            return;
        }

        $this->generateEntitiesEmails(
            $eventEmailsGeneratorHelper,
            $campaign,
            $campaignStep,
            $unprocessedEntities
        );
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGenerationHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @return OrderInterface[]
     */
    private function getUnprocessedEntities(
        EventEmailsGeneratorHelperInterface $eventEmailsGenerationHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep
    ) {
        $campaignStepId = $campaignStep->getEntityId();
        $processedEntitiesIds = $this->getProcessedEntitiesIds($campaignStepId, $eventEmailsGenerationHelper);

        return $eventEmailsGenerationHelper->getUnprocessedEntities(
            $campaign,
            $campaignStep,
            $processedEntitiesIds
        );
    }

    /**
     * @param int $campaignStepId
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @return array
     */
    private function getProcessedEntitiesIds($campaignStepId, $eventEmailsGeneratorHelper)
    {
        $mainEntityAttributeCode = $eventEmailsGeneratorHelper->getEntityIdAttributeCode();

        return $this->getEmailsAttributeValuesByCampaignStepId($campaignStepId, $mainEntityAttributeCode);
    }

    /**
     * @param int $campaignStepId
     * @param string $mainEntityAttributeCode
     * @return array
     */
    private function getEmailsAttributeValuesByCampaignStepId($campaignStepId, $mainEntityAttributeCode)
    {
        $emails = $this->getEmailsByCampaignStepId($campaignStepId);

        return $this->getUniqueEmailsAttributeValuesByEmails($emails, $mainEntityAttributeCode);

    }

    /**
     * @param int $campaignStepId
     * @return EmailInterface[]
     */
    private function getEmailsByCampaignStepId($campaignStepId)
    {
        $filters = [
            [EmailTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID, $campaignStepId]
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $emailsList = $this->getEmailSearchResult($searchCriteria);

        return $emailsList->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return EmailSearchResultsInterface
     */
    private function getEmailSearchResult(SearchCriteria $searchCriteria)
    {
        return $this->emailsRepository->getList($searchCriteria);
    }

    /**
     * @param EmailInterface[] $emails
     * @param string $attributeCode
     * @return array
     */
    private function getUniqueEmailsAttributeValuesByEmails($emails, $attributeCode)
    {
        $processedAttributeIds = [];

        foreach ($emails as $email) {
            $processedAttributeIds[] = $this->getEmailAttributeValue($email, $attributeCode);
        }

        return array_unique($processedAttributeIds);
    }

    /**
     * @param EmailInterface $emailsItem
     * @param string $attributeCode
     * @return int|string
     */
    private function getEmailAttributeValue(EmailInterface $emailsItem, $attributeCode)
    {
        $emailId = $emailsItem->getEntityId();
        $emailAttribute = $this->getEmailAttributeByEmailIdAndAttributeCode($emailId, $attributeCode);

        return $emailAttribute->getValue();
    }

    /**
     * @param int $emailId
     * @param string $attributeCode
     * @return EmailAttributeInterface|bool
     */
    private function getEmailAttributeByEmailIdAndAttributeCode($emailId, $attributeCode)
    {
        $filters = [
            [EmailAttributeTableInterface::COLUMN_NAME_EMAIL_ID, $emailId],
            [EmailAttributeTableInterface::COLUMN_NAME_ATTRIBUTE_CODE, $attributeCode]
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $emailAttributeList = $this->getEmailAttributeSearchResult($searchCriteria);
        $emailAttributes = $emailAttributeList->getItems();

        return reset($emailAttributes);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return EmailAttributeSearchResultsInterface
     */
    private function getEmailAttributeSearchResult(SearchCriteria $searchCriteria)
    {
        return $this->emailAttributesRepository->getList($searchCriteria);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param $unprocessedEntities
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateEntitiesEmails(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        $unprocessedEntities
    ) {
        foreach ($unprocessedEntities as $entity) {
            $this->generateEntityEmails($eventEmailsGeneratorHelper, $campaign, $campaignStep, $entity);
        }
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param $entity
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateEntityEmails(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        $entity
    ) {
        $baseEmail = $this->createEmail();

        $this->updateEmailForBase($baseEmail, $eventEmailsGeneratorHelper, $campaignStep, $entity);

        $this->updateAndSaveCustomerEmailIfRequired($eventEmailsGeneratorHelper, $campaign, $campaignStep, $entity, $baseEmail);
        $this->updateAndSaveAdditionalEmailsIfRequired($eventEmailsGeneratorHelper, $campaignStep, $baseEmail);
    }

    /**
     * @return EmailInterface
     */
    private function createEmail()
    {
        return $this->emailFactory->create();
    }

    /**
     * @param EmailInterface $email
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignStepInterface $campaignStep
     * @param mixed $entity
     * @return void
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function updateEmailForBase(
        EmailInterface $email,
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignStepInterface $campaignStep,
        $entity
    ) {
        $customerEmail = $eventEmailsGeneratorHelper->getCustomerEmailByEntity($entity);
        $campaignStepId = $campaignStep->getEntityId();
        $scheduleAt = $this->getScheduleAtDatetimeStringByContext($eventEmailsGeneratorHelper, $campaignStep, $entity);

        $emailAttributesData = $this->getEmailAttributesData(
            $eventEmailsGeneratorHelper,
            $campaignStep,
            $entity
        );

        $email
            ->setCustomerEmail($customerEmail)
            ->setCampaignStepId($campaignStepId)
            ->setScheduledAt($scheduleAt)
            ->setStatus(StatusInterface::STATUS_PENDING)
            ->setEmailAttributes($emailAttributesData)
        ;
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignStepInterface $campaignStep
     * @param mixed $entity
     * @return int|null
     */
    protected function getScheduleAtDatetimeStringByContext(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignStepInterface $campaignStep,
        $entity
    ) {
        $eventTimestamp = $eventEmailsGeneratorHelper->getEventTimestampByEntity($entity);

        if ($eventTimestamp === null) {
            return null;
        }

        $campaignStepsDelayPeriod = $campaignStep->getDelayPeriod();
        $unit = $campaignStep->getDelayUnit();

        return $this->getScheduleAtDatetimeString($eventTimestamp, $campaignStepsDelayPeriod, $unit);
    }

    /**
     * @param string $eventTimestamp
     * @param int $campaignStepsDelayPeriod
     * @param string $unit
     * @return int
     */
    private function getScheduleAtDatetimeString($eventTimestamp, $campaignStepsDelayPeriod, $unit)
    {
        $eventDateTimeString = date('Y-m-d H:i:s', $eventTimestamp);
        $scheduleAtTimestamp = strtotime("{$eventDateTimeString} + {$campaignStepsDelayPeriod} {$unit}" );

        return $scheduleAtTimestamp;
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignStepInterface $campaignStep
     * @param $entity
     * @return mixed
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getEmailAttributesData(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignStepInterface $campaignStep,
        $entity
    ) {
        $emailAttributesData = $eventEmailsGeneratorHelper->getEmailAttributesByEntity($entity);

        $cartPriceRuleId = $this->generateIndividualCartPriceRuleIfRequired(
            $campaignStep,
            $eventEmailsGeneratorHelper,
            $entity
        );

        if ($cartPriceRuleId) {
            $emailAttributesData[self::ATTRIBUTE_CODE_RULE_ID] = $cartPriceRuleId;
        }

        return $emailAttributesData;
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param $entity
     * @return int|null
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateIndividualCartPriceRuleIfRequired(
        CampaignStepInterface $campaignStep,
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $entity
    ) {
        $discountStatus = $campaignStep->getDiscountStatus();

        if (!$discountStatus) {
            return null;
        }

        $discountType = $campaignStep->getDiscountType();

        if ($discountType == DiscountTypeOptions::ACTION_TYPE_RULE_OPTION) {
            return $campaignStep->getSalesRuleId();
        } else {
            $customerEmail = $eventEmailsGeneratorHelper->getCustomerEmailByEntity($entity);
            $websiteId = $this->getWebsiteIdByEntity($eventEmailsGeneratorHelper, $entity);
            $discountAmount = $campaignStep->getDiscountPercent();
            $discountPeriod = $campaignStep->getDiscountPeriod();

            $rule = $this->createAndSaveIndividualRuleWithCouponCode(
                $customerEmail,
                $websiteId,
                $discountAmount,
                $discountPeriod,
                $discountType
            );

            return $rule->getRuleId();
        }
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param $entity
     * @return int
     * @throws NoSuchEntityException
     */
    private function getWebsiteIdByEntity(EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper, $entity)
    {
        $storeId = $eventEmailsGeneratorHelper->getStoreIdByEntity($entity);

        return $this->getWebsiteIdByStoreId($storeId);
    }

    /**
     * @param int $storeId
     * @return int
     * @throws NoSuchEntityException
     */
    private function getWebsiteIdByStoreId($storeId)
    {
        return $this->websiteHelper->getWebsiteIdByStoreId($storeId);
    }

    /**
     * @param $customerEmail
     * @param $websiteId
     * @param $discountPercent
     * @param $discountPeriod
     * @param $discountType
     * @return RuleInterface
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function createAndSaveIndividualRuleWithCouponCode(
        $customerEmail,
        $websiteId,
        $discountAmount,
        $discountPeriod,
        $discountType
    ) {
        $toDate = $this->getDiscountToDataString($discountPeriod);

        $rule = $this->createAndSaveCartPriceRule($customerEmail, $websiteId, $discountAmount, $toDate, $discountType);
        $ruleId = $rule->getRuleId();

        $this->createAndSaveCartPriceRuleCoupon($ruleId);

        return $rule;
    }

    /**
     * @param int $discountPeriodInDays
     * @return false|string
     */
    private function getDiscountToDataString($discountPeriodInDays)
    {
        $currentDateTime = $this->getCurrentDateTimeAsString();
        $discountToDataTimestamp = strtotime("{$currentDateTime} + {$discountPeriodInDays} days");

        return date('Y-m-d', $discountToDataTimestamp);
    }

    /**
     * @return string
     */
    private function getCurrentDateTimeAsString()
    {
        return $this->date->gmtDate();
    }

    /**
     * @param $customerEmail
     * @param $websiteId
     * @param $discountAmount
     * @param $toDate
     * @param $discountType
     * @return RuleInterface
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function createAndSaveCartPriceRule($customerEmail, $websiteId, $discountAmount, $toDate, $discountType)
    {
        $cartPriceRule = $this->createCartPriceRule();
        $this->updateCartPriceRuleByContext(
            $cartPriceRule,
            $customerEmail,
            $websiteId,
            $discountAmount,
            $toDate,
            $discountType
        );

        return $this->saveCartPriceRule($cartPriceRule);
    }

    /**
     * @return RuleInterface
     */
    private function createCartPriceRule()
    {
        return $this->ruleFactory->create();
    }

    /**
     * @param RuleInterface $ruleModel
     * @param $customerEmail
     * @param $websiteId
     * @param $discountAmount
     * @param $toDate
     * @param $discountType
     */
    private function updateCartPriceRuleByContext(
        RuleInterface $ruleModel,
        $customerEmail,
        $websiteId,
        $discountAmount,
        $toDate,
        $discountType
    ) {
        $cartPriceRuleName = $this->getCartPriceRuleNameByCustomerEmail($customerEmail);

        $ruleModel
            ->setName($cartPriceRuleName)
            ->setSimpleAction(
                $discountType == DiscountTypeOptions::ACTION_TYPE_PERCENT_OPTION
                    ? RuleInterface::DISCOUNT_ACTION_BY_PERCENT : RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT
            )
            ->setDiscountAmount($discountAmount)
            ->setUsesPerCustomer(1)
            ->setUsesPerCoupon(1)
            ->setCouponType(RuleInterface::COUPON_TYPE_SPECIFIC_COUPON)
            ->setUseAutoGeneration(1)
            ->setWebsiteIds([$websiteId])
            ->setCustomerGroupIds($this->getCustomerGroupsIds())
            ->setToDate($toDate)
            ->setIsActive(true);
    }

    /**
     * @param string $customerEmail
     * @return string
     */
    private function getCartPriceRuleNameByCustomerEmail($customerEmail)
    {
        return sprintf(self::CART_PRICE_RULE_NAME, $customerEmail);
    }

    /**
     * @return array
     */
    private function getCustomerGroupsIds()
    {
        return $this->customerGroupService->getCustomerGroupsIds();
    }

    /**
     * @param $cartPriceRule
     * @return RuleInterface
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function saveCartPriceRule($cartPriceRule)
    {
        return $this->ruleRepository->save($cartPriceRule);
    }

    /**
     * @param $ruleId
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function createAndSaveCartPriceRuleCoupon($ruleId)
    {
        $coupon = $this->createCartPriceRuleCoupon();
        $this->updateCartPriceRuleCouponByContext($coupon, $ruleId);
        $this->saveCartPriceRuleCoupon($coupon);
    }

    /**
     * @return CouponInterface
     */
    private function createCartPriceRuleCoupon()
    {
        return $this->couponFactory->create();
    }

    /**
     * @param CouponInterface $coupon
     * @param $ruleId
     */
    private function updateCartPriceRuleCouponByContext(CouponInterface $coupon, $ruleId)
    {
        $code = $this->generateCartPriceRuleCouponCode();

        $coupon
            ->setRuleId($ruleId)
            ->setCode($code)
            ->setUsageLimit(1)
            ->setUsagePerCustomer(1)
            ->setIsPrimary(false)
            ->setType(CouponHelper::COUPON_TYPE_SPECIFIC_AUTOGENERATED);
    }

    /**
     * @return string
     */
    private function generateCartPriceRuleCouponCode()
    {
        return $this->couponMassGenerator
            ->setData(self::COUPON_MASS_GENERATOR_DATA_KEY_LENGTH, static::CART_PRICE_RULE_COUPON_CODE_LENGTH)
            ->generateCode();
    }

    /**
     * @param CouponInterface $coupon
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputException
     */
    private function saveCartPriceRuleCoupon(CouponInterface $coupon)
    {
        $this->couponRepository->save($coupon);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param $entity
     * @param EmailInterface $baseEmail
     */
    private function updateAndSaveCustomerEmailIfRequired(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        $entity,
        EmailInterface $baseEmail
    ) {
        if (!$this->isSendEmailToCustomerRequired($eventEmailsGeneratorHelper, $campaign, $campaignStep, $entity)) {
            return;
        }

        $email = clone $baseEmail;
        $this->updateEmailForCustomer($email);

        $this->saveEmail($email);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param $entity
     * @return bool
     */
    private function isSendEmailToCustomerRequired(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        $entity
    ) {
        if (!$eventEmailsGeneratorHelper->isSendEmailToCustomerRequired($campaignStep)) {
            return false;
        }

        $customerEmail = $eventEmailsGeneratorHelper->getCustomerEmailByEntity($entity);
        $eventCode = $campaign->getEventCode();

        return $this->isCustomerSubscribedToEvent($customerEmail, $eventCode);
    }

    /**
     * @param string $customerEmail
     * @param string $eventCode
     * @return bool
     */
    private function isCustomerSubscribedToEvent($customerEmail, $eventCode)
    {
        $filters = [
            [UnsubscribedEmailAddressTableInterface::COLUMN_NAME_EVENT_CODE, $eventCode],
            [UnsubscribedEmailAddressTableInterface::COLUMN_NAME_CUSTOMER_EMAIL, $customerEmail],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        $unsubscribedList = $this->getUnsubscribedListSearchResult($searchCriteria);

        return !$unsubscribedList->getTotalCount();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return UnsubscribedEmailAddressSearchResultsInterface
     */
    private function getUnsubscribedListSearchResult(SearchCriteria $searchCriteria)
    {
        return $this->unsubscribedListRepository->getList($searchCriteria);
    }

    /**
     * @param EmailInterface $email
     */
    private function updateEmailForCustomer(
        EmailInterface $email
    ) {
        $unsubscribeCode = $this->generateUnsubscribeCode();

        $email
            ->setSecretCode($unsubscribeCode)
        ;
    }

    /**
     * @return string
     */
    private function generateUnsubscribeCode()
    {
        return $this->unsubscribeCodeService->generateUnsubscribeCode();
    }

    /**
     * @param EmailInterface $email
     * @return EmailInterface
     */
    private function saveEmail(EmailInterface $email)
    {
        return $this->emailsRepository->save($email);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $object
     * @param CampaignStepInterface $campaignStep
     * @param EmailInterface $baseEmail
     */
    private function updateAndSaveAdditionalEmailsIfRequired(
        EventEmailsGeneratorHelperInterface $object,
        CampaignStepInterface $campaignStep,
        EmailInterface $baseEmail
    ) {
        $emailAddresses = $object->getAdditionalEmailAddresses($campaignStep);

        if (!$emailAddresses) {
            return;
        }

        foreach ($emailAddresses as $email) {
            $this->updateAndSaveAdditionalEmail($email, $baseEmail);
        }
    }

    /**
     * @param string $emailAddress
     * @param EmailInterface $baseEmail
     * @return EmailInterface
     */
    private function updateAndSaveAdditionalEmail($emailAddress, EmailInterface $baseEmail)
    {
        $email = clone $baseEmail;
        $email->setCustomerEmail($emailAddress);

        return $this->saveEmail($email);
    }

    /**
     * Prepare emails for send
     *
     * @throws LocalizedException
     */
    public function sendOrHoldPendingToNowEmails()
    {
        $pendingToNowEmails = $this->getPendingToNowEmails();

        if (!$pendingToNowEmails) {
            return;
        }

        $this->sendOrHoldOrSkipEmails($pendingToNowEmails);
    }

    /**
     * @return EmailInterface[]
     */
    private function getPendingToNowEmails()
    {
        $currentDateTime = $this->getCurrentDateTimeAsString();
        $enabledCampaignStepsIds = $this->getEnabledCampaignStepsIds();

        $filters = [
            [
                [EmailTableInterface::COLUMN_NAME_SCHEDULED_AT, $currentDateTime, 'lteq'],
                [EmailTableInterface::COLUMN_NAME_SCHEDULED_AT, true, 'null']
            ],
            [EmailTableInterface::COLUMN_NAME_STATUS, StatusInterface::STATUS_PENDING],
            [EmailTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID, $enabledCampaignStepsIds, 'in']
        ];

        $sortOrders = [
            EmailTableInterface::COLUMN_NAME_SCHEDULED_AT => SortOrder::SORT_ASC
        ];

        $searchCriteria = $this->createSearchCriteria($filters, $sortOrders);

        $emailsList = $this->getEmailSearchResult($searchCriteria);

        return $emailsList->getItems();
    }

    /**
     * @return int[]
     */
    private function getEnabledCampaignStepsIds()
    {
        return $this->campaignStepService->getActiveCampaignStepsIds();
    }

    /**
     * @param EmailInterface[] $emails
     * @return array
     * @throws LocalizedException
     */
    public function sendOrHoldOrSkipEmails($emails)
    {
        $emailStatusStatistic = $this->getInitialEmailStatusStatisticArray();

        foreach ($emails as $email) {
            $this->sendOrHoldOrSkipEmail($email);

            $emailStatusStatistic = $this->updateStatusStatistic($email, $emailStatusStatistic);
        }

        return $emailStatusStatistic;
    }

    /**
     * @return array
     */
    private function getInitialEmailStatusStatisticArray()
    {
        return [
            StatusInterface::STATUS_PENDING => 0,
            StatusInterface::STATUS_SENT => 0,
            StatusInterface::STATUS_HOLD => 0,
            StatusInterface::STATUS_ERROR => 0,
        ];
    }

    /**
     * @param EmailInterface $email
     * @return EmailInterface
     * @throws LocalizedException
     */
    private function sendOrHoldOrSkipEmail(EmailInterface $email)
    {
        $campaignStep = $this->getCampaignStepByEmail($email);
        $campaign = $this->getCampaignByCampaignStep($campaignStep);
        $eventCode = $campaign->getEventCode();
        $eventsEmailGeneratorHelper = $this->getEventEmailsGeneratorHelperByEventCode($eventCode);

        if (!$eventsEmailGeneratorHelper) {
            return $email;
        }

        $emailAttributes = $this->getEmailAttributesValuesByEmail($email);

        if (!$this->isEventEnabled($eventCode)) {
            return $email;
        }

        if (!$this->canSendEmail($email, $eventsEmailGeneratorHelper, $campaignStep)) {
            $this->setAndSaveEmailStatusHold($email);

            return $email;
        }

        $eventCode = $campaign->getEventCode();

        if (!$this->isEmailCustomerSubscribed($email, $eventCode)) {
            return $email;
        }

        $this->sendEmailByEmail(
            $eventsEmailGeneratorHelper,
            $campaign,
            $campaignStep,
            $email,
            $emailAttributes
        );

        return $email;
    }

    /**
     * @param EmailInterface $email
     * @return CampaignStepInterface
     */
    private function getCampaignStepByEmail(EmailInterface $email)
    {
        $campaignStepId = $email->getCampaignStepId();

        return $this->getCampaignStepById($campaignStepId);
    }

    /**
     * @param $campaignStepId
     * @return CampaignStepInterface
     */
    private function getCampaignStepById($campaignStepId)
    {
        return $this->campaignStepsRepository->get($campaignStepId);
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @return CampaignInterface
     */
    private function getCampaignByCampaignStep(CampaignStepInterface $campaignStep)
    {
        $campaignId = $campaignStep->getCampaignId();

        return $this->getCampaignById($campaignId);
    }

    /**
     * @param int $campaignId
     * @return CampaignInterface
     */
    private function getCampaignById($campaignId)
    {
        return $this->campaignsRepository->get($campaignId);
    }

    /**
     * @param EmailInterface $email
     * @return array
     */
    private function getEmailAttributesValuesByEmail(EmailInterface $email)
    {
        $emailAttributes = $this->getEmailAttributesByEmail($email);

        return $this->getEmailAttributesValues($emailAttributes);
    }

    /**
     * @param EmailInterface $email
     * @return EmailAttributeInterface[]
     */
    private function getEmailAttributesByEmail(EmailInterface $email)
    {
        $emailId = $email->getEntityId();

        return $this->getEmailAttributesByEmailId($emailId);
    }

    /**
     * @param int $emailId
     * @return EmailAttributeInterface[]
     */
    private function getEmailAttributesByEmailId($emailId)
    {
        $filters = [
            [EmailAttributeTableInterface::COLUMN_NAME_EMAIL_ID, $emailId],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $emailAttributesSearchResults = $this->getEmailAttributeSearchResult($searchCriteria);

        return $emailAttributesSearchResults->getItems();
    }

    /**
     * @param EmailAttributeInterface[] $emailAttributes
     * @return array
     */
    private function getEmailAttributesValues($emailAttributes)
    {
        $emailAttributesValues = [];

        foreach ($emailAttributes as $emailAttribute) {
            $attributeCode = $emailAttribute->getAttributeCode();
            $attributeValue = $emailAttribute->getValue();
            $emailAttributesValues[$attributeCode] = $attributeValue;
        }

        return $emailAttributesValues;
    }

    /**
     * @param $eventCode
     * @return bool
     */
    private function isEventEnabled($eventCode)
    {
        return $this->eventManagement->isEventEnabled($eventCode);
    }

    /**
     * @param EmailInterface $email
     * @param EventEmailsGeneratorHelperInterface $eventEmailGeneratorHelper
     * @param CampaignStepInterface $campaignStep
     * @return bool
     */
    private function canSendEmail(
        EmailInterface $email,
        EventEmailsGeneratorHelperInterface $eventEmailGeneratorHelper,
        CampaignStepInterface $campaignStep
    ) {
        $emailAttributesValues = $this->getEmailAttributeValuesByEmail($email);

        return $eventEmailGeneratorHelper->canSendEmail($campaignStep, $emailAttributesValues);
    }

    /**
     * @param EmailInterface $email
     * @return array
     */
    private function getEmailAttributeValuesByEmail(EmailInterface $email) {
        $emailId = $email->getEntityId();

        return $this->getEmailAttributeValuesByEmailId($emailId);
    }

    /**
     * @param int $emailId
     * @return array
     */
    private function getEmailAttributeValuesByEmailId($emailId)
    {
        $emailAttributes = $this->getEmailAttributesByEmailId($emailId);

        return $this->getEmailAttributesValues($emailAttributes);
    }

    /**
     * @param EmailInterface $email
     */
    private function setAndSaveEmailStatusHold(EmailInterface $email)
    {
        $email->setStatus(StatusInterface::STATUS_HOLD);
        $this->saveEmail($email);
    }

    /**
     * @param EmailInterface $email
     * @param string $eventCode
     * @return bool
     */
    private function isEmailCustomerSubscribed(EmailInterface $email, $eventCode)
    {
        $toEmail = $email->getEmailAddress();

        return $this->isCustomerSubscribedToEvent($toEmail, $eventCode);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $emailGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param EmailInterface $email
     * @param array $emailAttributes
     * @return $this
     * @throws LocalizedException
     */
    private function sendEmailByEmail(
        EventEmailsGeneratorHelperInterface $emailGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        EmailInterface $email,
        $emailAttributes
    ) {
        $campaignStepTemplateId = $campaignStep->getTemplateId();
        $storeId = $this->getStoreId($emailGeneratorHelper, $email);
        $emailSenderContact = $campaign->getSender();
        $templateVars = $this->getEmailTemplateVars(
            $emailGeneratorHelper,
            $campaign,
            $campaignStep,
            $email,
            $emailAttributes
        );

        $toEmail = $email->getEmailAddress();
        $recipientName = $this->getCustomerNameByEmailAttributes($emailGeneratorHelper, $emailAttributes);

        $transport = $this
            ->transportBuilder
            ->setTemplateIdentifier($campaignStepTemplateId)
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
            ->setFrom($emailSenderContact)
            ->setTemplateVars($templateVars)
            ->addTo($toEmail, $recipientName)
            ->getTransport();

        try {
            $transport->sendMessage();
            $this->updateEmailOnSendSuccess($email);
        } catch (MailException $e) {
            $this->updateEmailOnSendError($email);
            $this->logCritical($e);
        }

        return $this;
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $emailGeneratorHelper
     * @param EmailInterface $email
     * @return int
     */
    private function getStoreId(EventEmailsGeneratorHelperInterface $emailGeneratorHelper, EmailInterface $email)
    {
        $emailAttributesValues = $this->getEmailAttributesValuesByEmail($email);

        $entity = $this->getEntityByEmailAttributes($emailGeneratorHelper, $emailAttributesValues);

        return $emailGeneratorHelper->getStoreIdByEntity($entity);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param array $emailAttributes
     * @return mixed
     */
    private function getEntityByEmailAttributes(EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper, $emailAttributes)
    {
        return $this->emailHelper->getEntityByEmailAttributes($eventEmailsGeneratorHelper, $emailAttributes);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $emailGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param EmailInterface $email
     * @param array $emailAttributes
     * @return array
     * @throws LocalizedException
     */
    private function getEmailTemplateVars(
        EventEmailsGeneratorHelperInterface $emailGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        EmailInterface $email,
        $emailAttributes
    ) {
        $emailId = $email->getEntityId();
        $storeId = $this->getStoreIdByEmailAttributes($emailGeneratorHelper, $emailAttributes);
        $customerName = $this->getCustomerNameByEmailAttributes($emailGeneratorHelper, $emailAttributes);
        $moduleData = $this->getModuleData($emailGeneratorHelper, $campaign, $campaignStep, $email, $emailAttributes);
        $secretCode = $email->getSecretCode();
        $coupon = $this->getCouponByEmail($campaignStep, $emailAttributes, $storeId);

        $templateVars = [
            'store_id' => $storeId,
            'email_id' => $emailId,
            'customer_name' => $customerName,
            'secret_code' => $secretCode,
            'unsubscribe_code' => $secretCode,//for backward compatibility
            'coupon' => $coupon,
            'module_data' => $moduleData,
            self::AITOC_FOLLOW_UP_IDENTIFIER => true
        ];

        $templateVars = array_merge($templateVars, $emailAttributes);

        return $templateVars;
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $emailGeneratorHelper
     * @param $emailAttributes
     * @return int
     */
    private function getStoreIdByEmailAttributes(
        EventEmailsGeneratorHelperInterface $emailGeneratorHelper,
        $emailAttributes
    ) {
        return $this->emailHelper->getStoreIdByEmailAttributes($emailGeneratorHelper, $emailAttributes);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param EmailAttributeInterface[] $emailAttributes
     * @return string
     */
    private function getCustomerNameByEmailAttributes(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $emailAttributes
    ) {
        $entity = $this->getEntityByEmailAttributes($eventEmailsGeneratorHelper, $emailAttributes);

        $firstName = $eventEmailsGeneratorHelper->getCustomerFirstsNameByEntity($entity);
        $lastName = $eventEmailsGeneratorHelper->getCustomerLastNameByEntity($entity);

        return $this->getCustomerEmailName($firstName, $lastName);
    }

    /**
     * @param string|null $firstName
     * @param string|null $lastName
     * @return string
     */
    private function getCustomerEmailName($firstName, $lastName)
    {
        if (!$firstName && !$lastName) {
            return "Guest";
        }

        return "{$firstName} {$lastName}";
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $emailGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param EmailInterface $email
     * @param array $emailAttributes
     * @return DataObject
     */
    private function getModuleData(
        EventEmailsGeneratorHelperInterface $emailGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        EmailInterface $email,
        $emailAttributes
    ) {
        $moduleDataArray = $emailGeneratorHelper->getModuleData($campaign, $campaignStep, $email, $emailAttributes);

        $moduleData = new DataObject();
        $moduleData->addData($moduleDataArray);

        return $moduleData;
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @param $emailAttributes
     * @param $storeId
     * @return DataObject
     * @throws LocalizedException
     */
    private function getCouponByEmail(CampaignStepInterface $campaignStep, $emailAttributes, $storeId)
    {
        $coupon = new DataObject();

        if (!$this->isCouponDataRequired($campaignStep, $emailAttributes)) {
            return $coupon;
        }

        $couponData = $this->getCouponData($campaignStep, $emailAttributes, $storeId);
        $coupon->addData($couponData);

        return $coupon;
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @param array $emailAttributes
     * @return bool
     * @throws LocalizedException
     */
    private function isCouponDataRequired(CampaignStepInterface $campaignStep, $emailAttributes)
    {
        if (!$campaignStep->getDiscountStatus()) {
            return false;
        }

        $couponCode = $this->getCouponCodeByEmailAttributes($emailAttributes);

        if (!$couponCode) {
            return false;
        }

        return true;
    }

    /**
     * @param array $emailAttributes
     * @return null|string
     * @throws LocalizedException
     */
    private function getCouponCodeByEmailAttributes($emailAttributes)
    {
        $salesRuleId = isset($emailAttributes[self::ATTRIBUTE_CODE_RULE_ID]) ? $emailAttributes[self::ATTRIBUTE_CODE_RULE_ID] : null;

        $couponCode = $salesRuleId ? $this->getFirstCouponCodeByCartPriceRuleId($salesRuleId) : null;

        return $couponCode;
    }

    /**
     * @param int $salesRuleId
     * @return null|string
     * @throws LocalizedException
     */
    private function getFirstCouponCodeByCartPriceRuleId($salesRuleId)
    {
        $filters = [
            [self::ATTRIBUTE_CODE_RULE_ID, $salesRuleId]
        ];

        $coupons = $this->getCouponsByFilters($filters);
        $coupon = $coupons ? reset($coupons) : null;

        return  $coupon ? $coupon->getCode() : null;
    }

    /**
     * @param array $filters
     * @return CouponInterface[]
     * @throws LocalizedException
     */
    private function getCouponsByFilters($filters)
    {
        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getCouponsBySearchCriteria($searchCriteria);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CouponInterface[]
     * @throws LocalizedException
     */
    private function getCouponsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        return $this->getCouponSearchResult($searchCriteria)->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CouponSearchResultInterface
     * @throws LocalizedException
     */
    private function getCouponSearchResult(SearchCriteria $searchCriteria)
    {
        return $this->couponRepository->getList($searchCriteria);
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @param $emailAttributes
     * @param $storeId
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCouponData(CampaignStepInterface $campaignStep, $emailAttributes, $storeId)
    {
        $couponCode = $this->getCouponCodeByEmailAttributes($emailAttributes);
        $store = $this->websiteHelper->getStoreByStoreId($storeId);
        $currency = $this->currencyFactory->create()->load($store->getBaseCurrency()->getCode());
        $currencySymbol = $currency->getCurrencySymbol();

        if ($campaignStep->getDiscountType() == DiscountTypeOptions::ACTION_TYPE_RULE_OPTION) {
            $salesRule = $this->ruleRepository->getById($campaignStep->getSalesRuleId());
            switch ($salesRule->getSimpleAction()) {
                case \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION:
                    $discountAmountString = $salesRule->getDiscountAmount() .  $currencySymbol;
                    break;
                case \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION:
                    $discountAmountString = $salesRule->getDiscountAmount() .  '%';
                    break;
                default:
                    $discountAmountString = '';
            }
            $discountPeriod = $salesRule->getToDate();
        } else {
            $isFixed = $campaignStep->getDiscountType() == DiscountTypeOptions::ACTION_TYPE_FIXED_OPTION;
            $discountAmount = (int) $campaignStep->getDiscountPercent();
            $discountAmountString = "{$discountAmount}" . ($isFixed ? $currencySymbol : '%');
            $discountPeriod = $campaignStep->getDiscountPeriod();
        }

        return [
            'coupon_code' => $couponCode,
            'discount_amount' => $discountAmountString,
            'expiry_days' => $discountPeriod
        ];
    }

    /**
     * @param EmailInterface $email
     */
    private function updateEmailOnSendSuccess(EmailInterface $email)
    {
        $email->setStatus(StatusInterface::STATUS_SENT);
        $currentDateTime = $this->getCurrentDateTimeAsString();
        $email->setSentAt($currentDateTime);

        $this->saveEmail($email);
    }

    /**
     * @param EmailInterface $email
     */
    private function updateEmailOnSendError(EmailInterface $email)
    {
        $email->setStatus(StatusInterface::STATUS_ERROR);
        $this->saveEmail($email);
    }

    /**
     * @param $e
     */
    private function logCritical($e)
    {
        $this->_logger->critical($e);
    }

    /**
     * @param EmailInterface $email
     * @param array $emailStatusStatistic
     * @return array
     */
    private function updateStatusStatistic(EmailInterface $email, $emailStatusStatistic)
    {
        $emailStatus = $email->getStatus();
        $emailStatusStatistic[$emailStatus]++;

        return $emailStatusStatistic;
    }
}
