<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Setup\Operations\V100;

use Aitoc\FollowUpEmails\Api\Helper\BackendTemplateHelperInterface;
use Aitoc\FollowUpEmails\Api\Setup\InstallDataOperationInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class CreateEmailTemplatesForHeaderAndFooter
 */
class CreateEmailTemplatesForHeaderAndFooter implements InstallDataOperationInterface
{
    const EMAIL_TEMPLATE_HEADER_CODE = 'follow_up_header';
    const EMAIL_TEMPLATE_HEADER_NAME = 'Header (Follow Up Emails by Aitoc)';

    const EMAIL_TEMPLATE_FOOTER_CODE = 'follow_up_footer';
    const EMAIL_TEMPLATE_FOOTER_NAME = 'Footer (Follow Up Emails by Aitoc)';

    /**
     * @var BackendTemplateHelperInterface
     */
    private $backendTemplateHelper;

    /**
     * CreateEmailTemplatesForHeaderAndFooter constructor.
     * @param BackendTemplateHelperInterface $backendTemplateHelper
     */
    public function __construct(BackendTemplateHelperInterface $backendTemplateHelper)
    {
        $this->backendTemplateHelper = $backendTemplateHelper;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->addHeaderTemplate();
        $this->addFooterTemplate();
    }

    private function addHeaderTemplate()
    {
        $this->createEmailTemplateByCodeAndName(
            self::EMAIL_TEMPLATE_HEADER_CODE,
            self::EMAIL_TEMPLATE_HEADER_NAME
        );
    }

    /**
     * @param string $emailTemplateCode
     * @param string $emailTemplateName
     */
    private function createEmailTemplateByCodeAndName($emailTemplateCode, $emailTemplateName)
    {
        $this->backendTemplateHelper->createAndSaveByCodeAndName($emailTemplateCode, $emailTemplateName);
    }

    private function addFooterTemplate()
    {
        $this->createEmailTemplateByCodeAndName(
            self::EMAIL_TEMPLATE_FOOTER_CODE,
            self::EMAIL_TEMPLATE_FOOTER_NAME
        );
    }
}
