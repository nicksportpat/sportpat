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

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zend_Db_Select_Exception;

/**
 * Class GroupByColumnCollection
 */
class GroupByColumnCollection
{
    /**
     * @param AbstractCollection $collection
     * @param string $groupByColumnName
     * @param string $aggregationFunction
     * @throws Zend_Db_Select_Exception
     */
    public function groupByColumn(AbstractCollection $collection, $groupByColumnName, $aggregationFunction)
    {
        $select = $collection->getSelect();
        $select->group($groupByColumnName);

        $fromPart = $select->getPart(Select::FROM);

        $select->setPart(Select::COLUMNS, []);

        $mainTableName = $fromPart['main_table']['tableName'];

        $connection = $collection->getConnection();
        $tableColumns = $connection->describeTable($mainTableName);

        $tableColumnsNames = array_keys($tableColumns);

        $columns = [];

        foreach ($tableColumnsNames as $columnName) {
            $columns[$columnName]  = ($columnName != $groupByColumnName)
                ? "{$aggregationFunction}({$columnName})"
                : $columnName;
        }

        $select->columns($columns);
    }
}