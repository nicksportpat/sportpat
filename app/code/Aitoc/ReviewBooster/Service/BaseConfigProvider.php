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

use Aitoc\FollowUpEmails\Service\BaseConfigProvider as FueBaseConfigProvider;

/**
 * Class BaseConfigProvider
 */
abstract class BaseConfigProvider extends FueBaseConfigProvider
{
    /**
     * @param string $configPath
     * @param int|null $websiteId
     * @return int|null
     */
    protected function getIntOrNullScopeConfigValue($configPath, $websiteId = null)
    {
        $value = $this->getScopeConfigValue($configPath, $websiteId);

        return $this->castToIntIfNotNull($value);
    }

    /**
     * @param int|string|null $value
     * @return int|null
     */
    private function castToIntIfNotNull($value)
    {
        if ($value !== null) {
            $value = (int) $value;
        }

        return $value;
    }

    /**
     * @param string $configPath
     * @param int|null $websiteId
     * @return bool|null
     */
    protected function getBoolOrNullScopeConfigValue($configPath, $websiteId = null)
    {
        $value = $this->getScopeConfigValue($configPath, $websiteId);

        return $this->castToBoolIfNotNull($value);
    }

    /**
     * @param mixed $value
     * @return bool|null
     */
    private function castToBoolIfNotNull($value)
    {
        if ($value !== null) {
            $value = (bool) $value;
        }

        return $value;
    }
}
