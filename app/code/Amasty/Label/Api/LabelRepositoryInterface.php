<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Api;

/**
 * @api
 */
interface LabelRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Label\Api\Data\LabelInterface $label
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function save(\Amasty\Label\Api\Data\LabelInterface $label);

    /**
     * Get by id
     *
     * @param int $id
     * @return \Amasty\Label\Api\Data\LabelInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\Label\Api\Data\LabelInterface $label
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Label\Api\Data\LabelInterface $label);

    /**
     * Delete by id
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $id
     *
     * @return void
     */
    public function duplicateLabel($id);

    /**
     * Lists
     *
     * @return \Amasty\Label\Api\Data\LabelInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAll();

    /**
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function getModelLabel();
}
