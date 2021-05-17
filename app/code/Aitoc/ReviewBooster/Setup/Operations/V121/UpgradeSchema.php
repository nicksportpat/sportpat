<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V121;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V121\UpgradeSchema\ReviewImageTable\Create as CreateReviewImageTableOperation;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaOperationInterface
{
    /**
     * @var CreateReviewImageTableOperation
     */
    private $createReviewImageTableOperation;

    public function __construct(CreateReviewImageTableOperation $createReviewImageTableOperation)
    {
        $this->createReviewImageTableOperation = $createReviewImageTableOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->createReviewImageTable($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function createReviewImageTable(SchemaSetupInterface $setup)
    {
        $this->createReviewImageTableOperation->execute($setup);
    }
}
