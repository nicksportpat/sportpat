<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Model\ResourceModel\Email\Collection\Processor\Filter;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class OrderId
 */
class OrderId implements CustomFilterInterface
{
    const TABLE_NAME = 'aitoc_follow_up_emails_email_attributes';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * OrderId constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $value = $filter->getValue();

        $select = $collection->getSelect();

        $prefixedEmailAttributeTableName = $this->getPrefixedEmailAttributeTableName();

        $select
            ->join(
                ['email_attribute' => $prefixedEmailAttributeTableName],
                'main_table.entity_id = email_attribute.email_id',
                ['order_id' => 'value']
            )
            ->where('`email_attribute`.`attribute_code` = ?', 'order_id')
            ->where('`email_attribute`.`value` = ?', $value);
            //todo: rewrite!!!

        return true;
    }

    /**
     * @return string
     */
    private function getPrefixedEmailAttributeTableName()
    {
        return $this->resourceConnection->getTableName(self::TABLE_NAME);
    }
}
