<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Model;

use Aitoc\FollowUpEmails\Api\CoreConfigDataManagerInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\CoreConfigData as CoreConfigDataResource;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CoreConfigDataManager
 */
class CoreConfigDataManager implements CoreConfigDataManagerInterface
{
    /**
     * @var CoreConfigDataResource
     */
    private $coreConfigDataResource;

    /**
     * CoreConfigDataRepository constructor.
     * @param CoreConfigDataResource $coreConfigDataResource
     */
    public function __construct(CoreConfigDataResource $coreConfigDataResource)
    {
        $this->coreConfigDataResource = $coreConfigDataResource;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function isExists($path, $scopeType, $scopeId)
    {
        return $this->coreConfigDataResource->isExists($path, $scopeType, $scopeId);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function get($path, $scopeType, $scopeId)
    {
        return $this->coreConfigDataResource->getValue($path, $scopeType, $scopeId);
    }

    /**
     * @inheritdoc
     */
    public function set($path, $configValue, $scopeType, $scopeId)
    {
        $this->coreConfigDataResource->saveConfig($path, $configValue, $scopeType, $scopeId);
    }

    /**
     * @inheritdoc
     */
    public function delete($path, $scopeType, $scopeId)
    {
        $this->coreConfigDataResource->deleteConfig($path, $scopeType, $scopeId);
    }
}
