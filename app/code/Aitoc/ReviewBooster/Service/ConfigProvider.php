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

use Aitoc\ReviewBooster\Api\Service\ConfigProviderInterface;

/**
 * Class ConfigProvider
 */
class ConfigProvider extends BaseConfigProvider implements ConfigProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getOrderStatuses()
    {
        return $this->getScopeConfigValue(self::GENERAL_ORDER_STATUSES);
    }

    /**
     * @inheritdoc
     */
    public function getRatingNames($website = null)
    {
        $ratingNamesString = $this->getScopeConfigValue(self::GENERAL_RATING_NAMES, $website);

        $zeroBasedRatingNames = explode(',', $ratingNamesString);
        $oneBasedRatingNames = [];
        $count = count($zeroBasedRatingNames);

        for ($i = 0; $i < $count; $i++) {
            $oneBasedRatingNames[$i+1] = $zeroBasedRatingNames[$i];
        }

        return $oneBasedRatingNames;
    }

    /**
     * @inheritdoc
     */
    public function isAddRichSnippets($website = null)
    {
        return (bool) $this->getScopeConfigValue(self::GENERAL_ADD_RICH_SNIPPETS, $website);
    }

    /* NOTIFICATION */

    /**
     * @inheritdoc
     */
    public function isReviewEmailNotificationEnabled($website = null)
    {
        return (bool) $this->getScopeConfigValue(self::REVIEW_EMAIL_NOTIFICATION_ENABLED, $website);
    }

    /**
     * @inheritdoc
     */
    public function getReviewEmailNotificationRecipient($website = null)
    {
        return $this->getScopeConfigValue(self::REVIEW_EMAIL_NOTIFICATION_RECIPIENT, $website);
    }

    /* REVIEW */

    /**
     * @inheritdoc
     */
    public function isUploadImageEnabled($website = null)
    {
        return (bool) $this->getScopeConfigValue(self::REVIEW_IMAGES_ENABLED, $website);
    }

    /**
     * @inheritdoc
     */
    public function getImageWidth($website = null)
    {
        $width = (int) $this->getScopeConfigValue(self::REVIEW_IMAGES_WIDTH, $website);

        if (!$width) {
            $width = self::DEFAULT_IMAGE_WIDTH;
        }

        return $width;
    }

    /**
     * @inheritdoc
     */
    public function getImageHeight($website = null)
    {
        $height = (int) $this->getScopeConfigValue(self::REVIEW_IMAGES_HEIGHT, $website);

        if (!$height) {
            $height = self::DEFAULT_IMAGE_HEIGHT;
        }

        return $height;
    }
}
