<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Service;

use Exception;

/**
 * Class ConfigProviderForV130WithFallbackToOldConfigDefaultScope
 */
class ConfigProviderForV130WithFallbackToOldConfigDefaultScope extends ConfigProviderForV130WithFallbackToTableDefaultScope
{
    private $oldConfigValues = [
        self::GENERAL_EMAIL_SENDER => 'general',
        self::GENERAL_TEMPLATE_NAME => 'review_booster_general_settings_template',
        self::GENERAL_IGNORED_CUSTOMER_GROUPS => null,
        self::GENERAL_SEND_EMAILS_AUTOMATICALLY => false,
        self::GENERAL_DELAY_PERIOD_IN_DAYS => 1,
        self::GENERAL_ADD_RICH_SNIPPETS => false,
        self::GENERAL_RATING_NAMES => 'Terrible,Poor,Average,Very good,Excellent',

        self::DISCOUNT_GENERATE => false,
        self::DISCOUNT_PERCENT => 5,
        self::DISCOUNT_PERIOD_IN_DAYS => 3,

        self::NOTIFICATION_IS_ENABLED => false,
        self::NOTIFICATION_EMAIL_RECIPIENT => null,

        self::REVIEW_IS_UPLOAD_IMAGE_ENABLED => false,
        self::REVIEW_IMAGE_WIDTH => null,
        self::REVIEW_IMAGE_HEIGHT => null,
    ];

    /**
     * @param string $path
     * @param int|null $websiteId
     * @return mixed
     * @throws Exception
     */
    protected function getScopeConfigValue($path, $websiteId = null)
    {
        $value = parent::getScopeConfigValue($path, $websiteId);

        if ($value !== null) {
            return $value;
        }

        return $this->getOldConfigValue($path);
    }

    /**
     * @param string $path
     * @return mixed
     * @throws Exception
     */
    private function getOldConfigValue($path)
    {
        if (!array_key_exists($path, $this->oldConfigValues)) {
            throw new Exception("Invalid path: {$path}.");
        }

        return $this->oldConfigValues[$path];
    }
}