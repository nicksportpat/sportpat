<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V105\UpgradeSchema;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V105\UpgradeSchema\ReviewDetailsTable\AddImageColumn as AddImageColumnOperation;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class ReviewDetailsTable
 */
class ReviewDetailsTable implements UpgradeSchemaOperationInterface
{
    /**
     * @var AddImageColumnOperation
     */
    private $addImageColumnOperation;

    /**
     * ReviewDetailsTable constructor.
     * @param AddImageColumnOperation $addImageColumnOperation
     */
    public function __construct(AddImageColumnOperation $addImageColumnOperation)
    {
        $this->addImageColumnOperation = $addImageColumnOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->addImageColumn($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addImageColumn(SchemaSetupInterface $setup)
    {
        $this->addImageColumnOperation->execute($setup);
    }
}
