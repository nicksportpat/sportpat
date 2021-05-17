<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Service;

/**
 * Interface ConfigProviderForV130Interface
 */
interface ConfigProviderForV130Interface
{
    const GENERAL_EMAIL_SENDER = 'review_booster/general_settings/email_sender';
    const GENERAL_TEMPLATE_NAME = 'review_booster/general_settings/template';
    const GENERAL_IGNORED_CUSTOMER_GROUPS = 'review_booster/general_settings/ignore_group';
    const GENERAL_SEND_EMAILS_AUTOMATICALLY = 'review_booster/general_settings/send_emails_automatically';
    const GENERAL_DELAY_PERIOD_IN_DAYS = 'review_booster/general_settings/delay_period';
    const GENERAL_ADD_RICH_SNIPPETS = 'review_booster/general_settings/add_rich_snippets';
    const GENERAL_RATING_NAMES = 'review_booster/general_settings/rating_names';

    const DISCOUNT_GENERATE = 'review_booster/discount_settings/generate_discounts';
    const DISCOUNT_PERCENT = 'review_booster/discount_settings/discount_percent';
    const DISCOUNT_PERIOD_IN_DAYS = 'review_booster/discount_settings/discount_period';

    const NOTIFICATION_IS_ENABLED = 'review_booster/notification_settings/enable_notification';
    const NOTIFICATION_EMAIL_RECIPIENT = 'review_booster/notification_settings/email_recipient';

    const REVIEW_IS_UPLOAD_IMAGE_ENABLED = 'review_booster/review_settings/upload_image';
    const REVIEW_IMAGE_WIDTH = 'review_booster/review_settings/image_widht';
    const REVIEW_IMAGE_HEIGHT = 'review_booster/review_settings/image_height';

    const DEFAULT_IMAGE_WIDTH = 300;
    const DEFAULT_IMAGE_HEIGHT = 300;

    /**
     * @param null|int $websiteId
     * @return string
     */
    public function getEmailSender($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return string
     */
    public function getTemplateName($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return int[]
     */
    public function getIgnoredCustomerGroups($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return string
     */
    public function isSendEmailsAutomatically($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return int
     */
    public function getDelayPeriodInDays($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return bool
     */
    public function isAddRichSnippets($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return array [$ratingValue => $ratingName, ...]
     */
    public function getRatingNames($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return bool
     */
    public function isGenerateDiscount($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return int
     */
    public function getDiscountPercent($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return int
     */
    public function getDiscountPeriodInDays($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return bool
     */
    public function isEmailNotificationEnabled($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return bool
     */
    public function getEmailNotificationRecipient($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return bool
     */
    public function isUploadImageEnabled($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return int
     */
    public function getImageWidth($websiteId = null);

    /**
     * @param null|int $websiteId
     * @return int
     */
    public function getImageHeight($websiteId = null);
}
