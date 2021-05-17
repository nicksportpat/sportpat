<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Setup\Base;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeDataOperationInterface;
use Aitoc\FollowUpEmails\Api\CoreConfigDataManagerInterface;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigDataCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class TransferConfigs
 */
abstract class TransferConfigs implements UpgradeDataOperationInterface
{
    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ConfigDataCollectionFactory
     */
    private $configDataCollectionFactory;

    /**
     * @var CoreConfigDataManagerInterface
     */
    private $coreConfigDataRepository;

    /**
     * TransferConfigs constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     * @param StoreManagerInterface $storeManager
     * @param ConfigDataCollectionFactory $configDataCollectionFactory
     * @param CoreConfigDataManagerInterface $coreConfigDataRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        StoreManagerInterface $storeManager,
        ConfigDataCollectionFactory $configDataCollectionFactory,
        CoreConfigDataManagerInterface $coreConfigDataRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->storeManager = $storeManager;
        $this->configDataCollectionFactory = $configDataCollectionFactory;
        $this->coreConfigDataRepository = $coreConfigDataRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->migrateConfigs();
        $this->deleteUnusedConfigs();
    }

    /**
     * @throws LocalizedException
     */
    private function migrateConfigs() {
        $migrationConfigPathMap = $this->getMigrationConfigPathMap();

        foreach ($migrationConfigPathMap as $oldConfigPath => $newConfigPath) {
            $this->migrateConfigForWebsitesAndDefault($oldConfigPath, $newConfigPath);
        }
    }

    /**
     * @return array - [$oldPath1=> $newPath1, ...]
     */
    abstract protected function getMigrationConfigPathMap();

    /**
     * @param string $oldConfigPath
     * @param string $newConfigPath
     * @throws LocalizedException
     */
    private function migrateConfigForWebsitesAndDefault($oldConfigPath, $newConfigPath)
    {
        $this->migrateConfigForWebsites($oldConfigPath, $newConfigPath);
        $this->migrateConfigForDefault($oldConfigPath, $newConfigPath);
    }

    /**
     * @param string $oldConfigPath
     * @param string $newConfigPath
     * @throws LocalizedException
     */
    private function migrateConfigForWebsites($oldConfigPath, $newConfigPath)
    {
        $websiteIds = $this->getWebsiteIds();

        foreach ($websiteIds as $websiteId) {
            $this->migrateConfigIfRequired($oldConfigPath, $newConfigPath, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        }
    }

    /**
     * @return array
     */
    private function getWebsiteIds()
    {
        $websites = $this->getWebsites();

        $websiteIds = [];

        foreach ($websites as $website) {
            $websiteIds[] = $website->getId();
        }

        return $websiteIds;
    }

    /**
     * @param bool $withDefault
     * @param bool $codeKey
     * @return WebsiteInterface[]
     */
    private function getWebsites($withDefault = false, $codeKey = false)
    {
        return $this->storeManager->getWebsites($withDefault, $codeKey);
    }

    /**
     * @param string $oldPath
     * @param string $newPath
     * @param string $scopeType
     * @param int $scopeId
     * @throws LocalizedException
     */
    private function migrateConfigIfRequired($oldPath, $newPath, $scopeType, $scopeId = 0)
    {
        if (!$this->isConfigValueExists($oldPath, $scopeType, $scopeId)) {
            return;
        }

        $configValue = $this->getConfigValue($oldPath, $scopeType, $scopeId);
        $this->writeConfigValue($newPath, $configValue, $scopeType, $scopeId);
        $this->deleteConfigValue($oldPath, $scopeType, $scopeId);
    }

    /**
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     * @return bool
     * @throws LocalizedException
     */
    private function isConfigValueExists($path, $scopeType, $scopeId)
    {
        return $this->coreConfigDataRepository->isExists($path, $scopeType, $scopeId);
    }

    /**
     * @param string $configPath
     * @param string $scopeType
     * @param int $scopeId
     * @return string
     * @throws LocalizedException
     */
    private function getConfigValue(
        $configPath,
        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeId = 0
    ) {
        return $this->coreConfigDataRepository->get($configPath, $scopeType, $scopeId);
    }

    /**
     * @param string $configPath
     * @param mixed $value
     * @param string $scopeType
     * @param int $scopeId
     */
    private function writeConfigValue(
        $configPath,
        $value,
        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeId = 0
    ) {
        $this->coreConfigDataRepository->set($configPath, $value, $scopeType, $scopeId);
    }

    /**
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     * @return bool
     */
    private function deleteConfigValue($path, $scopeType, $scopeId = 0)
    {
        return $this->coreConfigDataRepository->delete($path, $scopeType, $scopeId);
    }

    /**
     * @param string $oldConfigPath
     * @param string $newConfigPath
     * @throws LocalizedException
     */
    private function migrateConfigForDefault($oldConfigPath, $newConfigPath)
    {
        $this->migrateConfigIfRequired($oldConfigPath, $newConfigPath, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }

    private function deleteUnusedConfigs()
    {
        $deletedConfigPaths = $this->getDeleteConfigPaths();

        foreach ($deletedConfigPaths as $deletedConfigPath) {
            $this->deleteConfigPathForWebsitesAndDefault($deletedConfigPath);
        }
    }

    /**
     * @return array
     */
    abstract  protected function getDeleteConfigPaths();
    /**
     * @param string $configPath
     */
    private function deleteConfigPathForWebsitesAndDefault($configPath)
    {
        $this->deleteConfigPathFromWebsites($configPath);
        $this->deleteConfigPathFromDefault($configPath);
    }

    /**
     * @param string $configPath
     */
    private function deleteConfigPathFromWebsites($configPath) {
        $websiteIds = $this->getWebsiteIds();

        foreach ($websiteIds as $websiteId) {
            $this->deleteConfigValue($configPath, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        }
    }

    /**
     * @param $configPath
     */
    private function deleteConfigPathFromDefault($configPath)
    {
        $this->deleteConfigValue($configPath, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }
}
