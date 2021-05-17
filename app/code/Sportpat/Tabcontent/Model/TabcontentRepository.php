<?php
namespace Sportpat\Tabcontent\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Sportpat\Tabcontent\Api\Data\TabcontentInterface;
use Sportpat\Tabcontent\Api\Data\TabcontentInterfaceFactory;
use Sportpat\Tabcontent\Api\Data\TabcontentSearchResultInterfaceFactory;
use Sportpat\Tabcontent\Api\TabcontentRepositoryInterface;
use Sportpat\Tabcontent\Model\ResourceModel\Tabcontent as TabcontentResourceModel;
use Sportpat\Tabcontent\Model\ResourceModel\Tabcontent\Collection;
use Sportpat\Tabcontent\Model\ResourceModel\Tabcontent\CollectionFactory as TabcontentCollectionFactory;

class TabcontentRepository implements TabcontentRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Manage Content resource model
     *
     * @var TabcontentResourceModel
     */
    protected $resource;

    /**
     * Manage Content collection factory
     *
     * @var TabcontentCollectionFactory
     */
    protected $tabcontentCollectionFactory;

    /**
     * Manage Content interface factory
     *
     * @var TabcontentInterfaceFactory
     */
    protected $tabcontentInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var TabcontentSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     * @param TabcontentResourceModel $resource
     * @param TabcontentCollectionFactory $tabcontentCollectionFactory
     * @param TabcontentnterfaceFactory $tabcontentInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param TabcontentSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        TabcontentResourceModel $resource,
        TabcontentCollectionFactory $tabcontentCollectionFactory,
        TabcontentInterfaceFactory $tabcontentInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        TabcontentSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource             = $resource;
        $this->tabcontentCollectionFactory = $tabcontentCollectionFactory;
        $this->tabcontentInterfaceFactory  = $tabcontentInterfaceFactory;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save Manage Content.
     *
     * @param \Sportpat\Tabcontent\Api\Data\TabcontentInterface $tabcontent
     * @return \Sportpat\Tabcontent\Api\Data\TabcontentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(TabcontentInterface $tabcontent)
    {
        /** @var TabcontentInterface|\Magento\Framework\Model\AbstractModel $tabcontent */
        try {
            $this->resource->save($tabcontent);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Manage Content: %1',
                $exception->getMessage()
            ));
        }
        return $tabcontent;
    }

    /**
     * Retrieve Manage Content
     *
     * @param int $tabcontentId
     * @return \Sportpat\Tabcontent\Api\Data\TabcontentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($tabcontentId)
    {
        if (!isset($this->instances[$tabcontentId])) {
            /** @var TabcontentInterface|\Magento\Framework\Model\AbstractModel $tabcontent */
            $tabcontent = $this->tabcontentInterfaceFactory->create();
            $this->resource->load($tabcontent, $tabcontentId);
            if (!$tabcontent->getId()) {
                throw new NoSuchEntityException(__('Requested Manage Content doesn\'t exist'));
            }
            $this->instances[$tabcontentId] = $tabcontent;
        }
        return $this->instances[$tabcontentId];
    }

    /**
     * Retrieve Manage Contents matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Sportpat\Tabcontent\Api\Data\TabcontentSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Sportpat\Tabcontent\Api\Data\TabcontentSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Sportpat\Tabcontent\Model\ResourceModel\Tabcontent\Collection $collection */
        $collection = $this->tabcontentCollectionFactory->create();

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
            $collection->addOrder('main_table.' . TabcontentInterface::TABCONTENT_ID, SortOrder::SORT_ASC);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var TabcontentInterface[] $tabcontents */
        $tabcontents = [];
        /** @var \Sportpat\Tabcontent\Model\Tabcontent $tabcontent */
        foreach ($collection as $tabcontent) {
            /** @var TabcontentInterface $tabcontentDataObject */
            $tabcontentDataObject = $this->tabcontentInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $tabcontentDataObject,
                $tabcontent->getData(),
                TabcontentInterface::class
            );
            $tabcontents[] = $tabcontentDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($tabcontents);
    }

    /**
     * Delete Manage Content
     *
     * @param TabcontentInterface $tabcontent
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(TabcontentInterface $tabcontent)
    {
        /** @var TabcontentInterface|\Magento\Framework\Model\AbstractModel $tabcontent */
        $id = $tabcontent->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($tabcontent);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to removeManage Content %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Manage Content by ID.
     *
     * @param int $tabcontentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($tabcontentId)
    {
        $tabcontent = $this->get($tabcontentId);
        return $this->delete($tabcontent);
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
