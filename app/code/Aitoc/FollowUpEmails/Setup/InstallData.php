<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Setup;

use Aitoc\FollowUpEmails\Setup\Base\InstallData as BaseInstallData;
use Aitoc\FollowUpEmails\Setup\Operations\v100\CreateEmailTemplatesForHeaderAndFooter as CreateEmailTemplatesForHeaderAndFooterOperation;
use Aitoc\FollowUpEmails\Setup\Operations\v100\PublishIcons as PublishIconsOperation;
use Exception;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 */
class InstallData extends BaseInstallData
{
    /**
     * @var PublishIconsOperation
     */
    private $publishIconsOperation;

    /**
     * @var CreateEmailTemplatesForHeaderAndFooterOperation
     */
    private $createEmailTemplatesForHeaderAndFooterOperation;

    /**
     * @param PublishIconsOperation $publishIconsOperation
     * @param CreateEmailTemplatesForHeaderAndFooterOperation $createEmailTemplatesForHeaderAndFooterOperation
     */
    public function __construct(
        PublishIconsOperation $publishIconsOperation,
        CreateEmailTemplatesForHeaderAndFooterOperation $createEmailTemplatesForHeaderAndFooterOperation
    ) {
        $this->publishIconsOperation = $publishIconsOperation;
        $this->createEmailTemplatesForHeaderAndFooterOperation = $createEmailTemplatesForHeaderAndFooterOperation;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->publishModuleIcons($setup);

        $this->beginTransaction($setup);

        try {
            $this->createEmailTemplatesForHeaderAndFooter($setup);
            $this->commitTransaction($setup);
        } catch (Exception $exception) {
            $this->rollBackTransaction($setup);
            throw $exception;
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function publishModuleIcons(ModuleDataSetupInterface $setup)
    {
        $this->publishIconsOperation->execute($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function createEmailTemplatesForHeaderAndFooter(ModuleDataSetupInterface $setup)
    {
        $this->createEmailTemplatesForHeaderAndFooterOperation->execute($setup);
    }
}
