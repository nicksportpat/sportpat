<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\Repository;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Api\LabelRepositoryInterface;
use Amasty\Label\Model\LabelsFactory;
use Amasty\Label\Model\ResourceModel\Labels as LabelsResource;
use Amasty\Label\Model\ResourceModel\Labels\CollectionFactory;
use Amasty\Label\Model\ResourceModel\Labels\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LabelsRepository implements LabelRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var LabelsFactory
     */
    private $labelsFactory;

    /**
     * @var LabelsResource
     */
    private $labelsResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $labelss;

    /**
     * @var CollectionFactory
     */
    private $labelsCollectionFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $ioFile;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        LabelsFactory $labelsFactory,
        LabelsResource $labelsResource,
        CollectionFactory $labelsCollectionFactory,
        Filesystem $filesystem,
        File $ioFile
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->labelsFactory = $labelsFactory;
        $this->labelsResource = $labelsResource;
        $this->labelsCollectionFactory = $labelsCollectionFactory;
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
    }

    /**
     * @inheritdoc
     */
    public function save(LabelInterface $labels)
    {
        try {
            if ($labels->getId()) {
                $labels = $this->getById($labels->getId())->addData($labels->getData());
            }
            $this->labelsResource->save($labels);
            unset($this->labelss[$labels->getId()]);
        } catch (\Exception $e) {
            if ($labels->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save labels with ID %1. Error: %2',
                        [$labels->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new labels. Error: %1', $e->getMessage()));
        }

        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->labelss[$id])) {
            /** @var \Amasty\Label\Model\Labels $labels */
            $labels = $this->labelsFactory->create();
            $this->labelsResource->load($labels, $id);
            if (!$labels->getId()) {
                throw new NoSuchEntityException(__('Labels with specified ID "%1" not found.', $id));
            }
            $this->labelss[$id] = $labels;
        }

        return $this->labelss[$id];
    }

    /**
     * @inheritdoc
     */
    public function delete(LabelInterface $labels)
    {
        try {
            $this->labelsResource->delete($labels);
            unset($this->labelss[$labels->getId()]);
        } catch (\Exception $e) {
            if ($labels->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove labels with ID %1. Error: %2',
                        [$labels->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove labels. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $labelsModel = $this->getById($id);
        $this->delete($labelsModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\Label\Model\ResourceModel\Labels\Collection $labelsCollection */
        $labelsCollection = $this->labelsCollectionFactory->create();
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $labelsCollection);
        }
        $searchResults->setTotalCount($labelsCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $labelsCollection);
        }
        $labelsCollection->setCurPage($searchCriteria->getCurrentPage());
        $labelsCollection->setPageSize($searchCriteria->getPageSize());
        $labelss = [];
        /** @var LabelInterface $labels */
        foreach ($labelsCollection->getItems() as $labels) {
            $labelss[] = $this->getById($labels->getId());
        }
        $searchResults->setItems($labelss);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $labelsCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $labelsCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $labelsCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection $labelsCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $labelsCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $labelsCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? 'DESC' : 'ASC'
            );
        }
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function duplicateLabel($id)
    {
        $model = $this->getById($id);
        $model->setId(null);
        $model->setStatus(0);

        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            'amasty/amlabel/'
        );

        /* create new images*/
        $imagesTypes = ['prod', 'cat'];
        foreach ($imagesTypes as $type) {
            $field = $type . '_img';
            $oldName = $newName = $model->getData($field);
            $i = 0;
            while ($this->ioFile->fileExists($path . $newName)) {
                $newName = (++$i) . $newName;
            }
            $this->ioFile->cp($path . $oldName, $path . $newName);
            $model->setData($field, $newName);
        }

        $this->save($model);
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        $labelCollection = $this->labelsCollectionFactory->create();

        return $labelCollection->getItems();
    }

    /**
     * @return LabelInterface
     */
    public function getModelLabel()
    {
        return $this->labelsFactory->create();
    }
}
