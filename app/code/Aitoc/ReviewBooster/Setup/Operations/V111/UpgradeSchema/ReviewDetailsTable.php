<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V111\UpgradeSchema;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V111\UpgradeSchema\ReviewDetailsTable\AddAdminTitleColumn as AddAdminTitleColumnOperation;
use Aitoc\ReviewBooster\Setup\Operations\V111\UpgradeSchema\ReviewDetailsTable\AddCommentColumn as AddCommentColumnOperation;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class ReviewDetailsTable
 */
class ReviewDetailsTable implements UpgradeSchemaOperationInterface
{
    /**
     * @var AddCommentColumnOperation
     */
    private $addCommentColumnOperation;

    /**
     * @var AddAdminTitleColumnOperation
     */
    private $addAdminTitleColumnOperation;

    /**
     * ReviewDetailsTable constructor.
     * @param AddCommentColumnOperation $addCommentColumnOperation
     * @param AddAdminTitleColumnOperation $addAdminTitleColumnOperation
     */
    public function __construct(
        AddCommentColumnOperation $addCommentColumnOperation,
        AddAdminTitleColumnOperation $addAdminTitleColumnOperation
    ) {
        $this->addCommentColumnOperation = $addCommentColumnOperation;
        $this->addAdminTitleColumnOperation = $addAdminTitleColumnOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->addCommentColumn($setup);
        $this->addAdminTitleColumn($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addCommentColumn(SchemaSetupInterface $setup)
    {
        $this->addCommentColumnOperation->execute($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addAdminTitleColumn(SchemaSetupInterface $setup)
    {
        $this->addAdminTitleColumnOperation->execute($setup);
    }
}
