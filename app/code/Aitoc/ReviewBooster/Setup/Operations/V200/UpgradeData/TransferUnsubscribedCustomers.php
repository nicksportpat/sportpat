<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Setup\Operations\V200\UpgradeData;

use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\CampaignStepRepositoryInterface;
use Aitoc\FollowUpEmails\Api\CampaignStepProviderInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Email\StatusInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressInterfaceFactory;
use Aitoc\FollowUpEmails\Api\EmailRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\UpgradeDataOperationInterface;
use Aitoc\FollowUpEmails\Api\UnsubscribedEmailAddressRepositoryInterface;
use Aitoc\ReviewBooster\Api\Data\Source\ReviewBoosterEventInterface;
use Aitoc\ReviewBooster\Api\Setup\V130\CustomerAttributeCodeInterface;
use Exception;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class TransferUnsubscribedCustomers
 */
class TransferUnsubscribedCustomers implements UpgradeDataOperationInterface
{
    const IS_SUBSCRIBER_ATTRIBUTE_CODE = CustomerAttributeCodeInterface::IS_REVIEW_BOOSTER_SUBSCRIBER;

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;
    /**
     * @var UnsubscribedEmailAddressRepositoryInterface
     */
    protected $unsubscribedListRepository;
    /**
     * @var CollectionFactory
     */
    private $customerCollectionFactory;
    /**
     * @var UnsubscribedEmailAddressInterfaceFactory
     */
    private $unsubscribedEmailAddressFactory;
    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var EmailRepositoryInterface
     */
    private $emailRepository;

    /**
     * @var SearchCriteriaBuilderInterface
     */
    private $searchCriteriaBuilderHelper;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @var CampaignStepRepositoryInterface
     */
    private $campaignStepRepository;

    /**
     * @var CampaignStepProviderInterface
     */
    private $campaignStepsProvider;

    /**
     * TransferUnsubscribedCustomers constructor.
     * @param CollectionFactory $customerCollectionFactory
     * @param UnsubscribedEmailAddressInterfaceFactory $unsubscribedEmailAddressFactory
     * @param DateTime $date
     * @param CustomerSetupFactory $customerSetupFactory
     * @param UnsubscribedEmailAddressRepositoryInterface $unsubscribedListRepository
     * @param EmailRepositoryInterface $emailRepository
     * @param SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper
     * @param CampaignRepositoryInterface $campaignRepository
     * @param CampaignStepRepositoryInterface $campaignStepRepository
     * @param CampaignStepProviderInterface $campaignStepsProvider
     */
    public function __construct(
        CollectionFactory $customerCollectionFactory,
        UnsubscribedEmailAddressInterfaceFactory $unsubscribedEmailAddressFactory,
        DateTime $date,
        CustomerSetupFactory $customerSetupFactory,
        UnsubscribedEmailAddressRepositoryInterface $unsubscribedListRepository,
        EmailRepositoryInterface $emailRepository,
        SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper,
        CampaignRepositoryInterface $campaignRepository,
        CampaignStepRepositoryInterface $campaignStepRepository,
        CampaignStepProviderInterface $campaignStepsProvider
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->unsubscribedEmailAddressFactory = $unsubscribedEmailAddressFactory;
        $this->date = $date;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->unsubscribedListRepository = $unsubscribedListRepository;
        $this->emailRepository = $emailRepository;
        $this->searchCriteriaBuilderHelper = $searchCriteriaBuilderHelper;
        $this->campaignRepository = $campaignRepository;
        $this->campaignStepRepository = $campaignStepRepository;
        $this->campaignStepsProvider = $campaignStepsProvider;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     * @throws Exception
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $customerSetup = $this->createCustomerSetup();
        $customers = $this->getNotReviewBoosterSubscriberCustomers();

        if (!$customers) {
            return;
        }

        $this->createAndSaveUnsubscribedEmailAddressesByCustomers($customers);
        $this->removeIsReviewBoosterSubscriberAttribute($customerSetup);
    }

    /**
     * @return CustomerSetup
     */
    private function createCustomerSetup()
    {
        return $this->customerSetupFactory->create();
    }

    /**
     * @return DataObject[]|Customer[]
     * @throws LocalizedException
     */
    private function getNotReviewBoosterSubscriberCustomers()
    {
        $customerCollection = $this->createCustomerCollection();
        $customerCollection->addAttributeToFilter( self::IS_SUBSCRIBER_ATTRIBUTE_CODE, 0);

        return $customerCollection->getItems();
    }

    /**
     * @return Collection
     */
    private function createCustomerCollection()
    {
        return $this->customerCollectionFactory->create();
    }

    /**
     * @param Customer[] $customers
     * @throws Exception
     */
    private function createAndSaveUnsubscribedEmailAddressesByCustomers($customers)
    {
        foreach ($customers as $customer) {
            $this->createAndSaveUnsubscribedEmailAddressByCustomer($customer);
        }
    }

    /**
     * @param Customer $customer
     * @throws Exception
     */
    private function createAndSaveUnsubscribedEmailAddressByCustomer(Customer $customer)
    {
        $customerEmail = $customer->getEmail();
        $unsubscribedEmailAddress = $this->createUnsubscribedEmailAddress();
        $emailId = $this->getLastSentReviewBoosterEmailIdByCustomerEmail($customerEmail);

        $unsubscribedEmailAddress
            ->setEventCode(ReviewBoosterEventInterface::EVENT_CODE)
            ->setCustomerEmail($customerEmail)
            ->setEmailId($emailId)
        ;

        $this->saveUnsubscribedEmailAddress($unsubscribedEmailAddress);
    }

    /**
     * @return UnsubscribedEmailAddressInterface
     */
    private function createUnsubscribedEmailAddress()
    {
        return $this->unsubscribedEmailAddressFactory->create();
    }

    /**
     * @param string $customerEmail
     * @return int|null
     * @throws Exception
     */
    private function getLastSentReviewBoosterEmailIdByCustomerEmail($customerEmail)
    {
        $email = $this->getLastSentReviewBoosterEmailByCustomerEmail($customerEmail);

        return $email? $email->getEntityId() : null;
    }

    /**
     * @param string $customerEmail
     * @return EmailInterface|null
     */
    private function getLastSentReviewBoosterEmailByCustomerEmail($customerEmail)
    {
        $campaignStepIds = $this->getReviewBoosterCampaignStepIds();

        $filters = [
            [EmailTableInterface::COLUMN_NAME_CUSTOMER_EMAIL, $customerEmail],
            [EmailTableInterface::COLUMN_NAME_STATUS, StatusInterface::STATUS_SENT],
            //Because user can unsubscribe only from sent emails.
            [EmailTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID, $campaignStepIds, 'in']
            //Because before migrating RB to v 2 other FUE submodules maybe installed (with their campaign steps).
        ];

        $orders = [
            EmailTableInterface::COLUMN_NAME_SENT_AT => SortOrder::SORT_DESC,
        ];

        $searchCriteria = $this->createSearchCriteria($filters, $orders);
        $searchCriteria->setPageSize(1);

        $emails = $this->getEmailsBySearchCriteria($searchCriteria);

        return $emails ? reset($emails) : null;
    }

    /**
     * @return array
     */
    private function getReviewBoosterCampaignStepIds()
    {
        $reviewBoosterCampaigns = $this->getReviewBoosterCampaigns();

        $stepIds = [];

        foreach ($reviewBoosterCampaigns as $reviewBoosterCampaign) {
            $campaignSteps = $this->getCampaignStepsByCampaign($reviewBoosterCampaign);

            $campaignStepIds = $this->getCampaignStepIds($campaignSteps);

            $stepIds = array_merge($stepIds, $campaignStepIds);
        }

        return $stepIds;
    }

    /**
     * @return CampaignInterface[]
     */
    private function getReviewBoosterCampaigns()
    {
        $filters = [
            [CampaignTableInterface::COLUMN_NAME_EVENT_CODE, ReviewBoosterEventInterface::EVENT_CODE]
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getCampaignsBySearchCriteria($searchCriteria);
    }

    /**
     * @param array $filters
     * @param array $orders
     * @return SearchCriteria
     */
    private function createSearchCriteria($filters, $orders = [])
    {
        return $this->searchCriteriaBuilderHelper->createSearchCriteria($filters, $orders);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CampaignInterface[]
     */
    private function getCampaignsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $searchResults = $this->getCampaignSearchResults($searchCriteria);

        return $searchResults->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CampaignSearchResultsInterface
     */
    private function getCampaignSearchResults(SearchCriteria $searchCriteria)
    {
        return $this->campaignRepository->getList($searchCriteria);
    }

    /**
     * @param CampaignInterface $campaign
     * @return CampaignStepInterface[]
     */
    private function getCampaignStepsByCampaign(CampaignInterface $campaign)
    {
        return $this->campaignStepsProvider->getCampaignStepsByCampaign($campaign);
    }

    /**
     * @param CampaignStepInterface[] $campaignSteps
     * @return int[]
     */
    private function getCampaignStepIds($campaignSteps)
    {
        return array_keys($campaignSteps);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return EmailInterface[]
     */
    private function getEmailsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $emailSearchResults = $this->getEmailsSearchResults($searchCriteria);

        return $emailSearchResults->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return EmailSearchResultsInterface
     */
    private function getEmailsSearchResults(SearchCriteria $searchCriteria)
    {
        return $this->emailRepository->getList($searchCriteria);
    }

    /**
     * @param UnsubscribedEmailAddressInterface $unsubscribedEmailAddress
     */
    private function saveUnsubscribedEmailAddress(UnsubscribedEmailAddressInterface $unsubscribedEmailAddress)
    {
        $this->unsubscribedListRepository->save($unsubscribedEmailAddress);
    }

    /**
     * @param CustomerSetup $customerSetup
     */
    private function removeIsReviewBoosterSubscriberAttribute(CustomerSetup $customerSetup)
    {
        $customerSetup->removeAttribute(Customer::ENTITY, self::IS_SUBSCRIBER_ATTRIBUTE_CODE);
    }
}
