<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Website
 */
class Website implements WebsiteInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Website constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $storeId
     * @return int
     * @throws NoSuchEntityException
     */
    public function getWebsiteIdByStoreId($storeId)
    {
        $store = $this->getStoreByStoreId($storeId);
        $storeGroupId = $store->getStoreGroupId();
        $storeGroup = $this->getStoreGroupById($storeGroupId);

        return $storeGroup->getWebsiteId();
    }

    /**
     * @param $storeGroupId
     * @return GroupInterface
     */
    private function getStoreGroupById($storeGroupId)
    {
        return $this->storeManager->getGroup($storeGroupId);
    }

    /**
     * @param int $storeId
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStoreByStoreId($storeId)
    {
        return $this->storeManager->getStore($storeId);
    }
}
