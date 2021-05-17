<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Plugin\Review\Model\ResourceModel\Review;

use Aitoc\ReviewBooster\Api\Data\Source\Table\ReviewDetailInterface as ReviewDetailTableInterface;
use Aitoc\ReviewBooster\Api\Data\Source\Table\ReviewInterface as ReviewTableInterface;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

/**
 * Class CollectionPlugin
 */
class Collection
{
    /**
     * @param ReviewCollection $subject
     * @param string|array $field
     * @param null|string|array $condition
     * @return array|null
     */
    public function beforeAddFieldToFilter(ReviewCollection $subject, $field, $condition)
    {
        $fieldName = is_array($field) ? reset($field) : $field;

        if (!$this->isFieldWithoutTableName($fieldName)) {
            return null;
        }

        $tableName = $this->getFieldTableName($fieldName);

        if (!$tableName) {
            return null;
        }

        $fieldNameWithTableName = $this->getFieldNameWithTableName($fieldName, $tableName);

        $fieldWithTableName = is_array($field)
            ? $field[key($field)] = $fieldNameWithTableName
            : $fieldNameWithTableName;

        return [
            $fieldWithTableName,
            $condition,
        ];
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    private function isFieldWithoutTableName($fieldName)
    {
        return strpos($fieldName , '.') === false;
    }

    /**
     * @param string $fieldName
     * @return int|null|string
     */
    private function getFieldTableName($fieldName)
    {
        $tablesColumns = $this->getTablesColumns();
        
        foreach($tablesColumns as $tableName => $tableColumns) {
            if (in_array($fieldName, $tableColumns)) {
                return $tableName;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    private function getTablesColumns()
    {
        return [
            'main_table' => [//`review`
                ReviewTableInterface::COLUMN_NAME_REVIEW_ID,
                ReviewTableInterface::COLUMN_NAME_CREATED_AT,
                ReviewTableInterface::COLUMN_NAME_ENTITY_ID,
                ReviewTableInterface::COLUMN_NAME_ENTITY_PK_VALUE,
                ReviewTableInterface::COLUMN_NAME_STATUS_ID,
            ],

            'detail' => [//`review_detail`
                ReviewDetailTableInterface::COLUMN_NAME_DETAIL_ID,
                ReviewDetailTableInterface::COLUMN_NAME_REVIEW_ID,
                ReviewDetailTableInterface::COLUMN_NAME_STORE_ID,
                ReviewDetailTableInterface::COLUMN_NAME_TITLE,
                ReviewDetailTableInterface::COLUMN_NAME_DETAIL,
                ReviewDetailTableInterface::COLUMN_NAME_NICKNAME,
                ReviewDetailTableInterface::COLUMN_NAME_CUSTOMER_ID,
            ]
        ];
    }

    /**
     * @param string $fieldName
     * @param string $tableName
     * @return string
     */
    private function getFieldNameWithTableName($fieldName, $tableName)
    {
        return "{$tableName}.{$fieldName}";
    }
}