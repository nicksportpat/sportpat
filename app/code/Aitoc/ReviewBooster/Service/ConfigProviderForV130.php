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

use Aitoc\ReviewBooster\Api\Service\ConfigProviderForV130Interface;

/**
 * Class ConfigProviderForV130
 */
class ConfigProviderForV130 extends BaseConfigProvider implements ConfigProviderForV130Interface
{
    /**
     * @inheritdoc
     */
    public function getEmailSender($websiteId = null)
    {
        return $this->getScopeConfigValue(self::GENERAL_EMAIL_SENDER, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateName($websiteId = null)
    {
        return $this->getScopeConfigValue(self::GENERAL_TEMPLATE_NAME, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getIgnoredCustomerGroups($websiteId = null)
    {
        $commaSeparatedExcludedGroupIdsString = $this->getScopeConfigValue(self::GENERAL_IGNORED_CUSTOMER_GROUPS, $websiteId);

        return $commaSeparatedExcludedGroupIdsString
            ? explode(',', $commaSeparatedExcludedGroupIdsString)
            : [];
    }

    /**
     * @inheritdoc
     */
    public function isSendEmailsAutomatically($websiteId = null)
    {
        return $this->getBoolOrNullScopeConfigValue(self::GENERAL_SEND_EMAILS_AUTOMATICALLY, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getDelayPeriodInDays($websiteId = null)
    {
        return $this->getIntOrNullScopeConfigValue(self::GENERAL_DELAY_PERIOD_IN_DAYS, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function isAddRichSnippets($websiteId = null)
    {
        return $this->getBoolOrNullScopeConfigValue(self::GENERAL_ADD_RICH_SNIPPETS, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getRatingNames($websiteId = null)
    {
        $ratingNamesString = $this->getScopeConfigValue(self::GENERAL_RATING_NAMES, $websiteId);

        $zeroBasedRatingNames = explode(',', $ratingNamesString);
        $oneBasedRatingNames = [];
        $count = count($zeroBasedRatingNames);

        for ($i = 0; $i < $count; $i++) {
            $oneBasedRatingNames[$i+1] = $zeroBasedRatingNames[$i];
        }

        return $oneBasedRatingNames;
    }

    /* NOTIFICATION */

    /**
     * @inheritdoc
     */
    public function isGenerateDiscount($websiteId = null)
    {
        return $this->getBoolOrNullScopeConfigValue(self::DISCOUNT_GENERATE, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getDiscountPercent($websiteId = null)
    {
        return $this->getIntOrNullScopeConfigValue(self::DISCOUNT_PERCENT, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getDiscountPeriodInDays($websiteId = null)
    {
        return $this->getIntOrNullScopeConfigValue(self::DISCOUNT_PERIOD_IN_DAYS, $websiteId);
    }

    /* NOTIFICATION */

    /**
     * @inheritdoc
     */
    public function isEmailNotificationEnabled($websiteId = null)
    {
        return $this->getBoolOrNullScopeConfigValue(self::NOTIFICATION_IS_ENABLED, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getEmailNotificationRecipient($websiteId = null)
    {
        return $this->getBoolOrNullScopeConfigValue(self::NOTIFICATION_EMAIL_RECIPIENT, $websiteId);
    }

    /* REVIEW */

    /**
     * @inheritdoc
     */
    public function isUploadImageEnabled($websiteId = null)
    {
        return $this->getBoolOrNullScopeConfigValue(self::REVIEW_IS_UPLOAD_IMAGE_ENABLED, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getImageWidth($websiteId = null)
    {
        $width = (int) $this->getScopeConfigValue(self::REVIEW_IMAGE_WIDTH, $websiteId);

        if (!$width) {
            $width = self::DEFAULT_IMAGE_WIDTH;
        }

        return $width;
    }

    /**
     * @inheritdoc
     */
    public function getImageHeight($websiteId = null)
    {
        $height = (int) $this->getScopeConfigValue(self::REVIEW_IMAGE_HEIGHT, $websiteId);

        if (!$height) {
            $height = self::DEFAULT_IMAGE_HEIGHT;
        }

        return $height;
    }
}
