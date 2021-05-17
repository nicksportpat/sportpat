<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Setup\Operations\v100;

use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Aitoc\FollowUpEmails\Api\Setup\InstallDataOperationInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class PublishIcons
 */
class PublishIcons implements InstallDataOperationInterface
{
    const DEFAULT_DIRECTORY_MODE = 0755;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * CreateIcons constructor.
     * @param Reader $reader
     * @param Filesystem $fileSystem
     */
    public function __construct(Reader $reader, Filesystem $fileSystem)
    {
        $this->reader = $reader;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $moduleIconsDir = $this->getModuleIconsDirectory();
        $mediaIconsDir = $this->getMediaIconsDir();

        $this->createDirectoryRecursiveIfNotExists($mediaIconsDir, self::DEFAULT_DIRECTORY_MODE);

        $icons = scandir($moduleIconsDir);

        foreach ($icons as $icon) {
            $this->copyFileIfRequired($moduleIconsDir, $mediaIconsDir, $icon);
        }
    }

    /**
     * @return string
     */
    private function getMediaIconsDir()
    {
        $mediaDir = $this->getMediaDir();

        return $mediaDir . DIRECTORY_SEPARATOR . 'followup'.DIRECTORY_SEPARATOR .'icons/';
    }

    /**
     * @param string $mediaIconsDir
     * @param int $mode
     */
    private function createDirectoryRecursiveIfNotExists($mediaIconsDir, $mode = 0777)
    {
        if (!file_exists($mediaIconsDir)) {
            mkdir($mediaIconsDir, $mode, true);
        }
    }

    /**
     * @return string
     */
    private function getMediaDir()
    {
        return $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath();
    }

    /**
     * @return string
     */
    private function getModuleIconsDirectory()
    {
        return $this->getModulePath() . '/view/base/web/images/icons/';
    }

    /**
     * @return string
     */
    public function getModulePath()
    {
        return $this->reader->getModuleDir('', "Aitoc_FollowUpEmails");
    }

    /**
     * @param $moduleIconsDir
     * @param $mediaIconsDir
     * @param $icon
     */
    private function copyFileIfRequired($moduleIconsDir, $mediaIconsDir, $icon)
    {
        $moduleIconFilename = $moduleIconsDir . $icon;
        $mediaIconFilename = $mediaIconsDir . $icon;

        if (!is_dir($moduleIconFilename) && (!file_exists($mediaIconFilename))) {
            copy($moduleIconFilename, $mediaIconFilename);
        }
    }
}
