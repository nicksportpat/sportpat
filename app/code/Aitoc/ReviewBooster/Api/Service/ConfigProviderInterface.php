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
 * Interface ConfigProviderInterface
 */
interface ConfigProviderInterface
{
    const GENERAL_ORDER_STATUSES = 'review_booster/general/order_statuses';
    const GENERAL_RATING_NAMES = 'review_booster/general/rating_names';
    const GENERAL_ADD_RICH_SNIPPETS = 'review_booster/general/add_rich_snippets';

    const REVIEW_EMAIL_NOTIFICATION_ENABLED = 'review_booster/review_email_notification/enabled';
    const REVIEW_EMAIL_NOTIFICATION_RECIPIENT = 'review_booster/review_email_notification/recipient';

    const REVIEW_IMAGES_ENABLED = 'review_booster/review_images/enabled';
    const REVIEW_IMAGES_WIDTH = 'review_booster/review_images/width';
    const REVIEW_IMAGES_HEIGHT = 'review_booster/review_images/height';

    const DEFAULT_IMAGE_WIDTH = 300;
    const DEFAULT_IMAGE_HEIGHT = 300;

    /**
     * @return string
     */
    public function getOrderStatuses();

    /**
     * @param int|null $website
     * @return array
     */
    public function getRatingNames($website = null);

    /**
     * @param int|null $website
     * @return bool
     */
    public function isAddRichSnippets($website = null);

    /* NOTIFICATION */

    /**
     * @param int|null $website
     * @return bool
     */
    public function isReviewEmailNotificationEnabled($website = null);

    /**
     * @param int|null $website
     * @return bool
     */
    public function getReviewEmailNotificationRecipient($website = null);


    /* REVIEW */

    /**
     * @param int|null $website
     * @return bool
     */
    public function isUploadImageEnabled($website = null);

    /**
     * @param int|null $website
     * @return int
     */
    public function getImageWidth($website = null);

    /**
     * @param int|null $website
     * @return int
     */
    public function getImageHeight($website = null);
}
