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

use Aitoc\FollowUpEmails\Api\Setup\UpgradeDataOperationInterface;
use Aitoc\FollowUpEmails\Setup\Base\TransferConfigs as BaseTransferConfigsOperation;
use Aitoc\ReviewBooster\Api\Service\ConfigProviderForV130Interface;
use Aitoc\ReviewBooster\Api\Service\ConfigProviderInterface as ConfigHelperInterface;

/**
 * Class TransferConfigs
 */
class TransferConfigs extends BaseTransferConfigsOperation implements UpgradeDataOperationInterface
{
    /**
     * @return array - [$oldPath1=> $newPath1, ...]
     */
    protected function getMigrationConfigPathMap()
    {
        return [
            ConfigProviderForV130Interface::GENERAL_ADD_RICH_SNIPPETS => ConfigHelperInterface::GENERAL_ADD_RICH_SNIPPETS,
            ConfigProviderForV130Interface::GENERAL_RATING_NAMES => ConfigHelperInterface::GENERAL_RATING_NAMES,
            ConfigProviderForV130Interface::NOTIFICATION_IS_ENABLED => ConfigHelperInterface::REVIEW_EMAIL_NOTIFICATION_ENABLED,
            ConfigProviderForV130Interface::NOTIFICATION_EMAIL_RECIPIENT => ConfigHelperInterface::REVIEW_EMAIL_NOTIFICATION_RECIPIENT,
            ConfigProviderForV130Interface::REVIEW_IS_UPLOAD_IMAGE_ENABLED => ConfigHelperInterface::REVIEW_IMAGES_ENABLED,
            ConfigProviderForV130Interface::REVIEW_IMAGE_WIDTH => ConfigHelperInterface::REVIEW_IMAGES_WIDTH,
            ConfigProviderForV130Interface::REVIEW_IMAGE_HEIGHT => ConfigHelperInterface::REVIEW_IMAGES_HEIGHT,
        ];
    }

    /**
     * @return array
     */
    protected function getDeleteConfigPaths()
    {
        return [
            //general
            ConfigProviderForV130Interface::GENERAL_EMAIL_SENDER,
            ConfigProviderForV130Interface::GENERAL_TEMPLATE_NAME,
            ConfigProviderForV130Interface::GENERAL_IGNORED_CUSTOMER_GROUPS,
            ConfigProviderForV130Interface::GENERAL_SEND_EMAILS_AUTOMATICALLY,
            ConfigProviderForV130Interface::GENERAL_DELAY_PERIOD_IN_DAYS,
            ConfigProviderForV130Interface::GENERAL_ADD_RICH_SNIPPETS,
            ConfigProviderForV130Interface::GENERAL_RATING_NAMES,

            //discount
            ConfigProviderForV130Interface::DISCOUNT_GENERATE,
            ConfigProviderForV130Interface::DISCOUNT_PERCENT,
            ConfigProviderForV130Interface::DISCOUNT_PERIOD_IN_DAYS,

            //review notification
            ConfigProviderForV130Interface::NOTIFICATION_IS_ENABLED,
            ConfigProviderForV130Interface::NOTIFICATION_EMAIL_RECIPIENT,

            //review image
            ConfigProviderForV130Interface::REVIEW_IS_UPLOAD_IMAGE_ENABLED,
            ConfigProviderForV130Interface::REVIEW_IMAGE_WIDTH,
            ConfigProviderForV130Interface::REVIEW_IMAGE_HEIGHT,
        ];
    }
}
