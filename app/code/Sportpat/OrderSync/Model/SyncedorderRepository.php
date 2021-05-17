<?php
namespace Sportpat\OrderSync\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Sportpat\OrderSync\Api\Data\SyncedorderInterface;
use Sportpat\OrderSync\Api\Data\SyncedorderInterfaceFactory;
use Sportpat\OrderSync\Api\Data\SyncedorderSearchResultInterfaceFactory;
use Sportpat\OrderSync\Api\SyncedorderRepositoryInterface;
use Sportpat\OrderSync\Model\ResourceModel\Syncedorder as SyncedorderResourceModel;
use Sportpat\OrderSync\Model\ResourceModel\Syncedorder\Collection;
use Sportpat\OrderSync\Model\ResourceModel\Syncedorder\CollectionFactory as SyncedorderCollectionFactory;

class SyncedorderRepository implements SyncedorderRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Synced Order resource model
     *
     * @var SyncedorderResourceModel
     */
    protected $resource;

    /**
     * Synced Order collection factory
     *
     * @var SyncedorderCollectionFactory
     */
    protected $syncedorderCollectionFactory;

    /**
     * Synced Order interface factory
     *
     * @var SyncedorderInterfaceFactory
     */
    protected $syncedorderInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var SyncedorderSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     * @param SyncedorderResourceModel $resource
     * @param SyncedorderCollectionFactory $syncedorderCollectionFactory
     * @param SyncedordernterfaceFactory $syncedorderInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param SyncedorderSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        SyncedorderResourceModel $resource,
        SyncedorderCollectionFactory $syncedorderCollectionFactory,
        SyncedorderInterfaceFactory $syncedorderInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        SyncedorderSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource             = $resource;
        $this->syncedorderCollectionFactory = $syncedorderCollectionFactory;
        $this->syncedorderInterfaceFactory  = $syncedorderInterfaceFactory;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save Synced Order.
     *
     * @param \Sportpat\OrderSync\Api\Data\SyncedorderInterface $syncedorder
     * @return \Sportpat\OrderSync\Api\Data\SyncedorderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SyncedorderInterface $syncedorder)
    {
        /** @var SyncedorderInterface|\Magento\Framework\Model\AbstractModel $syncedorder */
        try {
            $this->resource->save($syncedorder);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Synced Order: %1',
                $exception->getMessage()
            ));
        }
        return $syncedorder;
    }

    /**
     * Retrieve Synced Order
     *
     * @param int $syncedorderId
     * @return \Sportpat\OrderSync\Api\Data\SyncedorderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($syncedorderId)
    {
        if (!isset($this->instances[$syncedorderId])) {
            /** @var SyncedorderInterface|\Magento\Framework\Model\AbstractModel $syncedorder */
            $syncedorder = $this->syncedorderInterfaceFactory->create();
            $this->resource->load($syncedorder, $syncedorderId);
            if (!$syncedorder->getId()) {
                throw new NoSuchEntityException(__('Requested Synced Order doesn\'t exist'));
            }
            $this->instances[$syncedorderId] = $syncedorder;
        }
        return $this->instances[$syncedorderId];
    }

    /**
     * Retrieve Synced Orders matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Sportpat\OrderSync\Api\Data\SyncedorderSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Sportpat\OrderSync\Api\Data\SyncedorderSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Sportpat\OrderSync\Model\ResourceModel\Syncedorder\Collection $collection */
        $collection = $this->syncedorderCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        } else {
            $collection->addOrder('main_table.' . SyncedorderInterface::SYNCEDORDER_ID, SortOrder::SORT_ASC);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var SyncedorderInterface[] $syncedorders */
        $syncedorders = [];
        /** @var \Sportpat\OrderSync\Model\Syncedorder $syncedorder */
        foreach ($collection as $syncedorder) {
            /** @var SyncedorderInterface $syncedorderDataObject */
            $syncedorderDataObject = $this->syncedorderInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $syncedorderDataObject,
                $syncedorder->getData(),
                SyncedorderInterface::class
            );
            $syncedorders[] = $syncedorderDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($syncedorders);
    }

    /**
     * Delete Synced Order
     *
     * @param SyncedorderInterface $syncedorder
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(SyncedorderInterface $syncedorder)
    {
        /** @var SyncedorderInterface|\Magento\Framework\Model\AbstractModel $syncedorder */
        $id = $syncedorder->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($syncedorder);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to removeSynced Order %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Synced Order by ID.
     *
     * @param int $syncedorderId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($syncedorderId)
    {
        $syncedorder = $this->get($syncedorderId);
        return $this->delete($syncedorder);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }

    /**
     * clear caches instances
     * @return void
     */
    public function clear()
    {
        $this->instances = [];
    }
}
