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

use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailTemplateSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Campaign\StatusInterface as CampaignStatusInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\CampaignStep\DelayUnitInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\CampaignStep\StatusInterface as CampaignStepStatusInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Email\StatusInterface as EmailStatusInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\EmailTemplate\TableColumnNameInterface;
use Aitoc\FollowUpEmails\Api\EmailTemplateRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Helper\BackendTemplateHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface;
use Aitoc\FollowUpEmails\Api\Setup\UpgradeDataOperationInterface;
use Aitoc\FollowUpEmails\Helper\CssFileContentProvider;
use Aitoc\FollowUpEmails\Model\Campaign;
use Aitoc\FollowUpEmails\Model\CampaignFactory;
use Aitoc\FollowUpEmails\Model\CampaignRepository;
use Aitoc\FollowUpEmails\Model\CampaignStep;
use Aitoc\FollowUpEmails\Model\CampaignStepFactory;
use Aitoc\FollowUpEmails\Model\CampaignStepRepository;
use Aitoc\FollowUpEmails\Model\Email;
use Aitoc\FollowUpEmails\Model\EmailFactory;
use Aitoc\FollowUpEmails\Model\EmailRepository;
use Aitoc\ReviewBooster\Api\Data\EmailAttributeCodeInterface;
use Aitoc\ReviewBooster\Api\Data\Source\OrderStatusInterface;
use Aitoc\ReviewBooster\Api\Data\Source\ReminderStatusInterface;
use Aitoc\ReviewBooster\Api\Data\Source\ReviewBoosterEventInterface;
use Aitoc\ReviewBooster\Api\Service\ConfigProviderForV130Interface;
use Aitoc\ReviewBooster\Api\Setup\V130\ReminderTableInterface;
use Aitoc\ReviewBooster\Service\ConfigProviderForV130WithFallbackToOldConfigDefaultScope;
use Exception;
use LogicException;
use Magento\Config\Model\ResourceModel\Config\Data\Collection as ConfigDataCollection;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigDataCollectionFactory;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroupCollection;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreRepository;
use Magento\Framework\Mail\Template\ConfigInterface as TemplateConfigInterface;

/**
 * Class TransferReminders
 */
class CreateCampaignsWithStepsAndReminders implements UpgradeDataOperationInterface
{
    /* Email Template */
    const EMAIL_TEMPLATE_CODE = 'review_booster_general_settings_template';
    const EMAIL_TEMPLATE_NAME = 'Review Booster - Order Alert';

    /* OTHER */
    const DEFAULT_CAMPAIGN_DESCRIPTION_FOR_WEBSITE = 'Migrated campaign for website "%s" (%s)';
    const DEFAULT_CAMPAIGN_DESCRIPTION_FOR_DEFAULT = 'Migrated campaign for Default';
    const DEFAULT_CAMPAIGN_NAME = 'Review Booster Migrated - %s';
    const DEFAULT_CAMPAIGN_STEP_NAME = 'Migrated - %s';
    const DEFAULT_TEMPLATE_NAME_TEMPLATE = '%s - Migrated';

    const CUSTOMER_GROUP_KEY_VALUE = 'value';

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var CampaignFactory
     */
    private $campaignFactory;

    /**
     * @var CampaignRepository
     */
    private $campaignsRepository;

    /**
     * @var StoreRepository
     */
    private $storeRepository;

    /**
     * @var CustomerGroupCollection
     */
    private $customerGroup;

    /**
     * @var EmailFactory
     */
    private $emailFactory;

    /**
     * @var EmailRepository
     */
    private $emailRepository;

    /**
     * @var CampaignStepFactory
     */
    private $campaignStepFactory;

    /**
     * @var CampaignStepRepository
     */
    private $campaignStepsRepository;

    /**
     * @var BackendTemplateHelperInterface
     */
    private $backendTemplateHelper;

    /**
     * @var CssFileContentProvider
     */
    private $cssFileContentProvider;

    /**
     * @var ConfigProviderForV130Interface
     */
    private $oldConfigProviderWithFallbackToOldConfigDefaultScope;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigDataCollectionFactory
     */
    private $configDataCollectionFactory;

    /**
     * @var SearchCriteriaBuilderInterface
     */
    private $searchCriteriaBuilder;

    /**
     * @var EmailTemplateRepositoryInterface
     */
    private $emailTemplateRepository;

    /**
     * @var TemplateConfigInterface
     */
    private $templateConfig;

    /**
     * @param ResourceConnection $resource
     * @param CampaignFactory $campaignFactory
     * @param CampaignRepository $campaignsRepository
     * @param StoreRepository $storeRepository
     * @param CustomerGroupCollection $customerGroup
     * @param EmailFactory $emailFactory
     * @param EmailRepository $emailsRepository
     * @param CampaignStepFactory $campaignStepFactory
     * @param CampaignStepRepository $campaignStepsRepository
     * @param BackendTemplateHelperInterface $backendTemplateHelper
     * @param CssFileContentProvider $cssFileContentProvider
     * @param ConfigProviderForV130WithFallbackToOldConfigDefaultScope $configProviderForV130
     * @param StoreManagerInterface $storeManager
     * @param ConfigDataCollectionFactory $configDataCollectionFactory
     * @param SearchCriteriaBuilderInterface $searchCriteriaBuilder
     * @param EmailTemplateRepositoryInterface $emailTemplateRepository
     * @param TemplateConfigInterface $templateConfig
     */
    public function __construct(
        ResourceConnection $resource,
        CampaignFactory $campaignFactory,
        CampaignRepository $campaignsRepository,
        StoreRepository $storeRepository,
        CustomerGroupCollection $customerGroup,
        EmailFactory $emailFactory,
        EmailRepository $emailsRepository,
        CampaignStepFactory $campaignStepFactory,
        CampaignStepRepository $campaignStepsRepository,
        BackendTemplateHelperInterface $backendTemplateHelper,
        CssFileContentProvider $cssFileContentProvider,
        ConfigProviderForV130WithFallbackToOldConfigDefaultScope $configProviderForV130,
        StoreManagerInterface $storeManager,
        ConfigDataCollectionFactory $configDataCollectionFactory,
        SearchCriteriaBuilderInterface $searchCriteriaBuilder,
        EmailTemplateRepositoryInterface $emailTemplateRepository,
        TemplateConfigInterface $templateConfig
    ) {
        $this->resource = $resource;
        $this->campaignFactory = $campaignFactory;
        $this->campaignsRepository = $campaignsRepository;
        $this->storeRepository = $storeRepository;
        $this->customerGroup = $customerGroup;
        $this->emailFactory = $emailFactory;
        $this->emailRepository = $emailsRepository;
        $this->campaignStepFactory = $campaignStepFactory;
        $this->campaignStepsRepository = $campaignStepsRepository;
        $this->backendTemplateHelper = $backendTemplateHelper;
        $this->cssFileContentProvider = $cssFileContentProvider;
        $this->oldConfigProviderWithFallbackToOldConfigDefaultScope = $configProviderForV130;
        $this->storeManager = $storeManager;
        $this->configDataCollectionFactory = $configDataCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->templateConfig = $templateConfig;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $oldConfigDataProvider = $this->oldConfigProviderWithFallbackToOldConfigDefaultScope;
        $websiteIds = $this->getWebsiteIds();

        foreach ($websiteIds as $websiteId) {
            if (!$this->isSomeWebsiteOldConfigsExists($websiteId)) {
                continue;
            }

            $this->upgradeByWebsiteConfig($setup, $oldConfigDataProvider, $websiteId);
        }

        $this->upgradeByDefaultConfig($setup, $oldConfigDataProvider);
        $this->dropReminderTableWithCommitTransactionOrThrow($setup);
    }

    /**
     * @return int[]
     */
    private function getWebsiteIds()
    {
        $websites = $this->getWebsites();

        return array_keys($websites);
    }

    /**
     * @param bool $withDefault
     * @return WebsiteInterface[]
     */
    private function getWebsites($withDefault = false)
    {
        return $this->storeManager->getWebsites($withDefault);
    }

    /**
     * @param int $websiteId
     * @return bool
     */
    private function isSomeWebsiteOldConfigsExists($websiteId)
    {
        $configDataCollection = $this->createConfigDataCollection();
        $configDataCollection
            ->addScopeFilter(ScopeInterface::SCOPE_WEBSITES, $websiteId, 'review_booster');

        return (bool) $configDataCollection->getSize();
    }

    /**
     * @return ConfigDataCollection
     */
    private function createConfigDataCollection()
    {
        return $this->configDataCollectionFactory->create();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ConfigProviderForV130Interface $oldConfigDataProvider
     * @param int $websiteId
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function upgradeByWebsiteConfig(ModuleDataSetupInterface $setup, $oldConfigDataProvider, $websiteId)
    {
        $websiteStoreIds = $this->getStoreIdsByWebsiteId($websiteId);

        $campaign = $this->createAndSaveCampaignByOldConfigProvider($oldConfigDataProvider, $websiteStoreIds, $websiteId);
        $campaignStep = $this->createAndSaveCampaignStepByOldConfigProvider($oldConfigDataProvider, $campaign, $websiteId);

        $this->transferReminders($setup, $campaignStep, $websiteStoreIds);
    }

    /**
     * @param int $websiteId
     * @return int[]
     */
    private function getStoreIdsByWebsiteId($websiteId)
    {
        $storeGroupIds = $this->getStoreGroupIdsByWebsiteId($websiteId);

        return $this->getStoreIdsByStoreGroupIds($storeGroupIds);
    }

    /**
     * @param int $websiteId
     * @return int[]
     */
    private function getStoreGroupIdsByWebsiteId($websiteId)
    {
        $storeGroups = $this->getStoreGroups();
        $storeGroupIds = [];

        foreach ($storeGroups as $storeGroupId => $storeGroup) {
            if ($storeGroup->getWebsiteId() != $websiteId) {
                continue;
            }

            $storeGroupIds[] = $storeGroupId;
        }

        return $storeGroupIds;
    }

    /**
     * @param bool $withDefault
     * @return GroupInterface[]
     */
    private function getStoreGroups($withDefault = false)
    {
        return $this->storeManager->getGroups($withDefault);
    }

    /**
     * @param $storeGroupIds
     * @return int[]
     */
    private function getStoreIdsByStoreGroupIds($storeGroupIds)
    {
        $stores = $this->getStores();
        $storeIds = [];

        foreach ($stores as $storeId => $store) {
            $storeGroupId = $store->getStoreGroupId();

            if (!in_array($storeGroupId, $storeGroupIds)) {
                continue;
            }

            $storeIds[] = $storeId;
        }

        return $storeIds;
    }

    /**
     * @param bool $withDefault
     * @return StoreInterface[]
     */
    private function getStores($withDefault = false)
    {
        return $this->storeManager->getStores($withDefault);
    }

    /**
     * @param ConfigProviderForV130Interface $oldConfigProvider
     * @param int[] $storeIds
     * @param int|null $websiteId
     * @return CampaignInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function createAndSaveCampaignByOldConfigProvider(
        ConfigProviderForV130Interface $oldConfigProvider,
        $storeIds,
        $websiteId = null
    ) {
        $campaign = $this->createCampaign();
        $campaign = $this->updateCampaignByOldConfigProvider($campaign, $oldConfigProvider, $storeIds, $websiteId);
        $this->saveCampaign($campaign);

        return $campaign;
    }

    /**
     * @return Campaign
     */
    private function createCampaign()
    {
        return $this->campaignFactory->create();
    }

    /**
     * @param CampaignInterface $campaign
     * @param ConfigProviderForV130Interface $oldConfigProvider
     * @param int[] $storeIds
     * @param int|null $websiteId
     * @return CampaignInterface
     * @throws LocalizedException
     */
    private function updateCampaignByOldConfigProvider(
        CampaignInterface $campaign,
        ConfigProviderForV130Interface $oldConfigProvider,
        $storeIds,
        $websiteId = null
    ) {
        $website = $websiteId ? $this->getWebsiteById($websiteId) : null;

        $campaignName = $this->getCampaignName($website);
        $campaignDescription = $this->getCampaignDescription($website);

        $oldConfigEmailSender = $oldConfigProvider->getEmailSender($websiteId);
        $customerGroupIds = $this->getApplicableCustomerGroupIds($oldConfigProvider, $websiteId);

        return $campaign
            ->setEventCode(ReviewBoosterEventInterface::EVENT_CODE)
            ->setStatus(CampaignStatusInterface::ENABLED)
            ->setName($campaignName)
            ->setDescription($campaignDescription)
            ->setSender($oldConfigEmailSender)
            ->setStoreIds($storeIds)
            ->setCustomerGroupIds($customerGroupIds)
        ;
    }

    /**
     * @param $websiteId
     * @return WebsiteInterface
     * @throws LocalizedException
     */
    private function getWebsiteById($websiteId)
    {
        return $this->storeManager->getWebsite($websiteId);
    }

    /**
     * @param WebsiteInterface $website
     * @return string
     */
    private function getCampaignName(WebsiteInterface $website = null)
    {
        $scopeCode = $website ? $website->getName() : 'Default';

        return sprintf(self::DEFAULT_CAMPAIGN_NAME, $scopeCode);
    }

    /**
     * @param WebsiteInterface $website
     * @return string
     */
    private function getCampaignDescription(WebsiteInterface $website = null)
    {
        return $website
            ? $this->getCampaignDescriptionForWebsite($website)
            : $this->getCampaignDescriptionForDefault();
    }

    /**
     * @param WebsiteInterface $website
     * @return string
     */
    private function getCampaignDescriptionForWebsite(WebsiteInterface $website)
    {
        $websiteName = $website->getName();
        $websiteCode = $website->getCode();

        return sprintf(self::DEFAULT_CAMPAIGN_DESCRIPTION_FOR_WEBSITE, $websiteName, $websiteCode);
    }

    /**
     * @return string
     */
    private function getCampaignDescriptionForDefault()
    {
        return self::DEFAULT_CAMPAIGN_DESCRIPTION_FOR_DEFAULT;
    }

    /**
     * @param ConfigProviderForV130Interface $oldConfigProvider
     * @param int|null $websiteId
     * @return int[]
     */
    private function getApplicableCustomerGroupIds(ConfigProviderForV130Interface $oldConfigProvider, $websiteId = null)
    {
        $ignoredCustomerGroups = $oldConfigProvider->getIgnoredCustomerGroups($websiteId);

        return ($ignoredCustomerGroups)
            ? $this->getReversedCustomerGroupIds($ignoredCustomerGroups)
            : $this->getCustomerGroupIds();
    }

    /**
     * get non-excluded customer group ids
     *
     * @param int[] $excludedGroupIds
     * @return array
     */
    private function getReversedCustomerGroupIds($excludedGroupIds)
    {
        $customerGroupIds = $this->getCustomerGroupIds();

        return array_diff($customerGroupIds, $excludedGroupIds);
    }

    /**
     * @return int[]
     */
    private function getCustomerGroupIds()
    {
        $customerGroups = $this->getCustomerGroupOptionArray();

        $customerGroupIds = [];

        foreach ($customerGroups as $customerGroup) {
            $customerGroupIds[] = $customerGroup[self::CUSTOMER_GROUP_KEY_VALUE];
        }

        return $customerGroupIds;
    }

    /**
     * @return array
     */
    private function getCustomerGroupOptionArray()
    {
        return $this->customerGroup->toOptionArray();
    }

    /**
     * @param CampaignInterface $campaign
     * @throws CouldNotSaveException
     */
    private function saveCampaign(CampaignInterface $campaign)
    {
        $this->campaignsRepository->save($campaign);
    }

    /**
     * @param ConfigProviderForV130Interface $oldConfigProvider
     * @param CampaignInterface $campaign
     * @param int|null $websiteId
     * @return Campaign|CampaignStep
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function createAndSaveCampaignStepByOldConfigProvider(
        ConfigProviderForV130Interface $oldConfigProvider,
        CampaignInterface $campaign,
        $websiteId = null
    ) {
        $campaignStep = $this->createCampaignStep();
        $this->updateCampaignStepByOldConfigProvider($campaignStep, $oldConfigProvider, $campaign, $websiteId);
        $this->saveCampaignStep($campaignStep);

        return $campaignStep;
    }

    /**
     * @return CampaignStep
     */
    private function createCampaignStep()
    {
        return $this->campaignStepFactory->create();
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @param ConfigProviderForV130Interface $oldConfigProvider
     * @param CampaignInterface $campaign
     * @param int|null $websiteId
     * @return CampaignStepInterface
     * @throws LocalizedException
     */
    private function updateCampaignStepByOldConfigProvider(
        CampaignStepInterface $campaignStep,
        ConfigProviderForV130Interface $oldConfigProvider,
        CampaignInterface $campaign,
        $websiteId = null
    ) {
        $campaignId = $campaign->getEntityId();
        $campaignStepName = $this->getCampaignStepName($websiteId);
        $campaignStepTemplateId = $this->getCampaignStepTemplateIdByOldConfigProvider($oldConfigProvider, $websiteId);
        $oldConfigDelayPeriodInDays = $oldConfigProvider->getDelayPeriodInDays($websiteId);
        $oldConfigIsGenerateDiscount = $oldConfigProvider->isGenerateDiscount($websiteId);
        $oldConfigDiscountPercent = $oldConfigProvider->getDiscountPercent($websiteId);
        $oldConfigDiscountPeriodInDays = $oldConfigProvider->getDiscountPeriodInDays($websiteId);

        return $campaignStep
            ->setStatus(CampaignStepStatusInterface::ENABLED)
            ->setCampaignId($campaignId)
            ->setName($campaignStepName)
            ->setTemplateId($campaignStepTemplateId)
            ->setDelayPeriod($oldConfigDelayPeriodInDays)
            ->setDelayUnit(DelayUnitInterface::DAYS)
            ->setDiscountStatus($oldConfigIsGenerateDiscount)
            ->setDiscountPercent($oldConfigDiscountPercent)
            ->setDiscountPeriod($oldConfigDiscountPeriodInDays);
    }

    /**
     * @param null $websiteId
     * @return string
     * @throws LocalizedException
     */
    private function getCampaignStepName($websiteId = null)
    {
        return $websiteId
            ? $this->getCampaignStepNameForWebsite($websiteId)
            : $this->getCampaignStepNameForDefault();
    }

    /**
     * @param int $websiteId
     * @return string
     * @throws LocalizedException
     */
    private function getCampaignStepNameForWebsite($websiteId)
    {
        $websiteName = $this->getWebsiteNameById($websiteId);

        return sprintf(self::DEFAULT_CAMPAIGN_STEP_NAME, $websiteName);
    }

    /**
     * @param int $websiteId
     * @return string
     * @throws LocalizedException
     */
    private function getWebsiteNameById($websiteId)
    {
        $website = $this->getWebsiteById($websiteId);

        return $website->getName();
    }

    /**
     * @return string
     */
    private function getCampaignStepNameForDefault()
    {
        return sprintf(self::DEFAULT_CAMPAIGN_STEP_NAME, 'Default');
    }

    /**
     * @param ConfigProviderForV130Interface $oldConfigProvider
     * @param int|null $websiteId
     * @return string
     */
    private function getCampaignStepTemplateIdByOldConfigProvider(ConfigProviderForV130Interface $oldConfigProvider, $websiteId = null)
    {
        $oldConfigTemplateId = $oldConfigProvider->getTemplateName($websiteId);

        if (is_numeric($oldConfigTemplateId)) {
            return $oldConfigTemplateId;
        }

        $migratedTemplateName = $this->getMigratedTemplateName($oldConfigTemplateId);
        $emailTemplate = $this->getEmailTemplateByOrigTemplateCodeAndName($oldConfigTemplateId, $migratedTemplateName);

        if ($emailTemplate) {
            return $emailTemplate->getTemplateId();
        }

        return $this->createEmailTemplateByCodeAndName($oldConfigTemplateId, $migratedTemplateName);
    }

    /**
     * @param string $oldConfigTemplateId
     * @return string
     */
    private function getMigratedTemplateName($oldConfigTemplateId)
    {
        $templateLabel = $this->getTemplateLabelById($oldConfigTemplateId);

        return sprintf(self::DEFAULT_TEMPLATE_NAME_TEMPLATE, $templateLabel);
    }

    /**
     * @param string $templateId
     * @return string
     */
    private function getTemplateLabelById($templateId)
    {
        return $this->templateConfig->getTemplateLabel($templateId);
    }

    /**
     * @param int|string $oldConfigTemplateId
     * @param string $migratedTemplateName
     * @return TemplateInterface|mixed|null
     */
    private function getEmailTemplateByOrigTemplateCodeAndName($oldConfigTemplateId, $migratedTemplateName)
    {
        $emailTemplates = $this->getEmailTemplatesByOrigTemplateCodeAndName($oldConfigTemplateId, $migratedTemplateName);

        return $emailTemplates ? reset($emailTemplates) : null;
    }

    /**
     * @param $oldConfigTemplateId
     * @param $migratedTemplateName
     * @return TemplateInterface[]
     */
    private function getEmailTemplatesByOrigTemplateCodeAndName($oldConfigTemplateId, $migratedTemplateName)
    {
        $filters = [
            [TableColumnNameInterface::ORIG_TEMPLATE_CODE, $oldConfigTemplateId],
            [TableColumnNameInterface::TEMPLATE_CODE, $migratedTemplateName]
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getEmailTemplatesBySearchCriteria($searchCriteria);
    }

    /**
     * @param array $filters
     * @return SearchCriteria
     */
    private function createSearchCriteria($filters)
    {
        return $this->searchCriteriaBuilder->createSearchCriteria($filters);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return TemplateInterface[]
     */
    private function getEmailTemplatesBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $searchResults = $this->getEmailTemplateSearchResults($searchCriteria);

        return $searchResults->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return EmailTemplateSearchResultsInterface
     */
    private function getEmailTemplateSearchResults(SearchCriteria $searchCriteria)
    {
        return $this->emailTemplateRepository->getList($searchCriteria);
    }

    /**
     * @param string $templateCode
     * @param string $templateName
     * @param string $styles
     * @return int
     */
    private function createEmailTemplateByCodeAndName($templateCode, $templateName, $styles = null)
    {
        return $this->backendTemplateHelper->createAndSaveByCodeAndName($templateCode, $templateName, $styles);
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @throws CouldNotSaveException
     */
    private function saveCampaignStep(CampaignStepInterface $campaignStep)
    {
        $this->campaignStepsRepository->save($campaignStep);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param CampaignStepInterface $campaignStep
     * @param int[] $storeIds
     * @throws CouldNotSaveException
     */
    private function transferReminders(ModuleDataSetupInterface $setup, CampaignStepInterface $campaignStep, $storeIds = [])
    {
        $campaignStepId = $campaignStep->getEntityId();

        $remindersData = $this->getRemindersData($setup, $storeIds);

        if (!$remindersData) {
            return;
        }

        $this->transferRemindersData($setup, $campaignStepId, $remindersData);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param int[] $storeIds
     * @return array
     */
    private function getRemindersData(ModuleDataSetupInterface $setup, $storeIds = [])
    {
        $reminderTable = $this->getPrefixedReminderTableName($setup);
        $dbAdapter = $setup->getConnection();
        $select = $dbAdapter->select()->from($reminderTable);

        if ($storeIds) {
            $storeIdColumnName = ReminderTableInterface::COLUMN_NAME_STORE_ID;
            $select->where("{$storeIdColumnName} IN (?)", $storeIds);
        }

        return $dbAdapter->fetchAll($select);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return string
     */
    private function getPrefixedReminderTableName(ModuleDataSetupInterface $setup)
    {
        return $setup->getTable(ReminderTableInterface::TABLE_NAME);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param int $campaignStepId
     * @param array $remindersData
     * @throws CouldNotSaveException
     */
    private function transferRemindersData(ModuleDataSetupInterface $setup, $campaignStepId, $remindersData)
    {
        foreach ($remindersData as $reminderData) {
            $this->createAndSaveEmailByReminderData($campaignStepId, $reminderData);
            $this->deleteReminder($setup, $reminderData);
        }
    }

    /**
     * @param int $campaignStepId
     * @param array $reminderData
     * @throws CouldNotSaveException
     * @throws Exception
     */
    private function createAndSaveEmailByReminderData($campaignStepId, $reminderData)
    {
        $email = $this->createEmail();
        $this->updateEmailByReminderData($email, $campaignStepId, $reminderData);
        $this->saveEmail($email);
    }

    /**
     * @return Email
     */
    private function createEmail()
    {
        return $this->emailFactory->create();
    }

    /**
     * @param EmailInterface $email
     * @param int $campaignStepId
     * @param array $reminderData
     * @throws Exception
     */
    private function updateEmailByReminderData(EmailInterface $email, $campaignStepId, $reminderData)
    {
        $reminderCustomerEmail = $reminderData[ReminderTableInterface::COLUMN_NAME_CUSTOMER_EMAIL];
        $reminderCreatedAt = $reminderData[ReminderTableInterface::COLUMN_NAME_CREATED_AT];
        $reminderSentAt = $reminderData[ReminderTableInterface::COLUMN_NAME_SENT_AT];

        $reminderStatus = $reminderData[ReminderTableInterface::COLUMN_NAME_STATUS];
        $emailStatus = $this->getEmailStatusByReminderStatus($reminderStatus);
        $unsubscribeCode = $reminderData[ReminderTableInterface::COLUMN_NAME_UNSUBSCRIBE_CODE];

        $reminderSalesRuleId = $reminderData[ReminderTableInterface::COLUMN_NAME_SALES_RULE_ID];
        $reminderOrderId = $reminderData[ReminderTableInterface::COLUMN_NAME_ORDER_ID];

        $attributes = [
            EmailAttributeCodeInterface::SALES_RULE_ID => $reminderSalesRuleId,
            EmailAttributeCodeInterface::ORDER_ID => $reminderOrderId,
            EmailAttributeCodeInterface::ORDER_STATUS => OrderStatusInterface::COMPLETE,
        ];

        $email
            ->setCampaignStepId($campaignStepId)
            ->setCustomerEmail($reminderCustomerEmail)
            ->setCreatedAt($reminderCreatedAt)
            ->setScheduledAt($reminderCreatedAt)
            ->setSentAt($reminderSentAt)
            ->setStatus($emailStatus)
            ->setSecretCode($unsubscribeCode)
            ->setEmailAttributes($attributes)
        ;
    }

    /**
     * @param string $oldStatusValue
     * @return int
     * @throws Exception
     */
    private function getEmailStatusByReminderStatus($oldStatusValue)
    {
        switch ($oldStatusValue) {
            case ReminderStatusInterface::PENDING:
                return EmailStatusInterface::STATUS_PENDING;
            case ReminderStatusInterface::SENT:
                return EmailStatusInterface::STATUS_SENT;
            case ReminderStatusInterface::FAILED:
                return EmailStatusInterface::STATUS_ERROR;
            default:
                throw new Exception('Invalid Reminder status value: '. $oldStatusValue);
        }
    }

    /**
     * @param EmailInterface $email
     * @throws CouldNotSaveException
     */
    private function saveEmail(EmailInterface $email)
    {
        $this->emailRepository->save($email);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param array $reminderData
     */
    private function deleteReminder(ModuleDataSetupInterface $setup, $reminderData)
    {
        $reminderId = $reminderData[ReminderTableInterface::COLUMN_NAME_ID];

        $this->deleteReminderById($setup, $reminderId);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param int $reminderId
     */
    private function deleteReminderById(ModuleDataSetupInterface $setup, $reminderId)
    {
        $dbAdapter = $setup->getConnection();
        $prefixedReminderTableName = $this->getPrefixedReminderTableName($setup);
        $dbAdapter->delete($prefixedReminderTableName, [ReminderTableInterface::COLUMN_NAME_ID . ' = ?' => $reminderId]);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ConfigProviderForV130Interface $oldConfigDataProvider
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function upgradeByDefaultConfig(ModuleDataSetupInterface $setup, $oldConfigDataProvider)
    {
        $storeIds = $this->getStoreIds();

        $campaign = $this->createAndSaveCampaignByOldConfigProvider($oldConfigDataProvider, $storeIds);
        $campaignStep = $this->createAndSaveCampaignStepByOldConfigProvider($oldConfigDataProvider, $campaign);

        $this->transferRemainsReminders($setup, $campaignStep);
    }

    /**
     * @param bool $withDefault
     * @return array
     */
    private function getStoreIds($withDefault = false)
    {
        $stores = $this->getStores($withDefault);

        return array_keys($stores);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param CampaignStepInterface $campaignStep
     * @throws CouldNotSaveException
     */
    private function transferRemainsReminders(ModuleDataSetupInterface $setup, CampaignStepInterface $campaignStep)
    {
        $this->transferReminders($setup, $campaignStep);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function dropReminderTableWithCommitTransactionOrThrow(ModuleDataSetupInterface $setup)
    {
        if (!$this->isReminderTableEmpty($setup)) {
            throw new LogicException('Reminder table not empty after migration.');
        }

        $this->dropReminderTableWithCommitTransaction($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return bool
     */
    private function isReminderTableEmpty(ModuleDataSetupInterface $setup)
    {
        $remindersData = $this->getRemindersData($setup);

        return !$remindersData;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function dropReminderTableWithCommitTransaction(ModuleDataSetupInterface $setup)
    {
        if ($this->isInTransaction($setup)) {
            $this->commitTransaction($setup);
        }

        $this->dropReminderTable($setup);

        $this->beginTransaction($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return bool
     */
    private function isInTransaction(ModuleDataSetupInterface $setup)
    {
        return (bool) $setup->getConnection()->getTransactionLevel();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function commitTransaction(ModuleDataSetupInterface $setup)
    {
        $setup->getConnection()->commit();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function dropReminderTable(ModuleDataSetupInterface $setup)
    {
        $prefixedReminderTableName = $this->getPrefixedReminderTableName($setup);
        $dbAdapter = $setup->getConnection();
        $dbAdapter->dropTable($prefixedReminderTableName);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function beginTransaction(ModuleDataSetupInterface $setup)
    {
        $setup->getConnection()->beginTransaction();
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    public function getStyleFileContent()
    {
        return $this->cssFileContentProvider->getCssContent();
    }
}
