<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Model\ResourceModel;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CoreConfigData
 */
class CoreConfigData extends Config
{
    /**
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     * @return bool
     * @throws LocalizedException
     */
    public function isExists($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0)
    {
        $select = $this->getSelect()->from(
            $this->getMainTable(),
            [$this->getIdFieldName()]
        )
            ->where('scope = :scope')
            ->where('scope_id = :scope_id')
            ->where('path = :path');

        $bind = [
            'scope' => $scopeType,
            'scope_id' => $scopeId,
            'path' => $path,
        ];

        $configId = $this->getConnection()->fetchOne($select, $bind);

        return (bool) $configId;
    }

    /**
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     * @return string
     * @throws LocalizedException
     */
    public function getValue($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0)
    {
        $select = $this->getSelect()
            ->from(
                $this->getMainTable(),
                ['value']
            )
            ->where('scope = :scope')
            ->where('scope_id = :scope_id')
            ->where('path = :path');

        $bind = [
            'scope' => $scopeType,
            'scope_id' => $scopeId,
            'path' => $path,
        ];

        return $this->getConnection()->fetchOne($select, $bind);
    }

    /**
     * @return Select
     */
    private function getSelect()
    {
        return $this->getConnection()->select();
    }
}
