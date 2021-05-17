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

use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignStepTableInterface;
use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CampaignStep
 */
class CampaignStep extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            CampaignStepTableInterface::TABLE_NAME,
            CampaignStepTableInterface::COLUMN_NAME_ENTITY_ID
        );
    }

    /**
     * @param array $ids
     * @return $this
     * @throws Exception
     */
    public function massDelete($ids)
    {
        $connection = $this->transactionManager->start($this->getConnection());
        try {
            $this->objectRelationProcessor->delete(
                $this->transactionManager,
                $connection,
                $this->getMainTable(),
                $this->getConnection()->quoteInto($this->getIdFieldName() . ' IN(?)', $ids),
                []
            );
            $this->transactionManager->commit();
        } catch (Exception $e) {
            $this->transactionManager->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);
        
        $options = $object->getData(CampaignStepTableInterface::COLUMN_NAME_OPTIONS);
        if (is_string($options)) {
            $options = json_decode($options, true);
        }

        $object->setData(CampaignStepTableInterface::COLUMN_NAME_OPTIONS, $options);
        $object->setDataChanges(false);

        return $this;
    }

    /**
     * Perform actions before object save
     *
     * @param AbstractModel|DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $options = $object->getData(CampaignStepTableInterface::COLUMN_NAME_OPTIONS);

        if (is_array($options)) {
            $options = json_encode($options);
            $object->setData(CampaignStepTableInterface::COLUMN_NAME_OPTIONS, $options);
        }

        return parent::_beforeSave($object);
    }
}
