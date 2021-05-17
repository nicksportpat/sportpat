<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V202;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V202\UpgradeSchema\ReviewDetailsTable as ReviewDetailsTableUpgradeSchemaOperation;
use Aitoc\ReviewBooster\Setup\Operations\V202\UpgradeSchema\ReviewDetailsTable\RenameColumns as ReviewDetailsTableRenameColumnsOperation;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaOperationInterface
{
    /**
     * @var ReviewDetailsTableRenameColumnsOperation
     */
    private $reviewDetailsTableOperation;

    /**
     * UpgradeSchema constructor.
     * @param ReviewDetailsTableUpgradeSchemaOperation $reviewDetailsTableOperation
     */
    public function __construct(ReviewDetailsTableUpgradeSchemaOperation $reviewDetailsTableOperation)
    {
        $this->reviewDetailsTableOperation = $reviewDetailsTableOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->reviewDetailsTableUpgradeSchema($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function reviewDetailsTableUpgradeSchema(SchemaSetupInterface $setup)
    {
        $this->reviewDetailsTableOperation->execute($setup);
    }
}
