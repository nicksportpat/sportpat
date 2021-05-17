<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class CoreConfigDataManager
 */
interface CoreConfigDataManagerInterface
{
    /**
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     * @return bool
     * @throws LocalizedException
     */
    public function isExists($path, $scopeType, $scopeId);

    /**
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     * @return string
     * @throws LocalizedException
     */
    public function get($path, $scopeType, $scopeId);

    /**
     * @param string $path
     * @param mixed $configValue
     * @param string $scopeType
     * @param int $scopeId
     * @return
     */
    public function set($path, $configValue, $scopeType, $scopeId);

    /**
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     */
    public function delete($path, $scopeType, $scopeId);
}