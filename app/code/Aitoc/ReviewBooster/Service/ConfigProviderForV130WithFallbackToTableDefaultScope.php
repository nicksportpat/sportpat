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

/**
 * Class ConfigProviderForV130WithFallbackToDefault
 *
 * If value for websiteId is null, then value for default scope in 'core_config_data' returned.
 */
class ConfigProviderForV130WithFallbackToTableDefaultScope extends ConfigProviderForV130
{
    /**
     * @param string $path
     * @param int|null $websiteId
     * @return mixed
     */
    protected function getScopeConfigValue($path, $websiteId = null)
    {
        $value = parent::getScopeConfigValue($path, $websiteId);

        if (($websiteId === null) || ($value !== null)) {
            return $value;
        }

        return parent::getScopeConfigValue($path);
    }
}
