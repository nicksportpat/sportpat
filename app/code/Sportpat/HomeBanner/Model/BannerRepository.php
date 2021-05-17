<?php
namespace Sportpat\HomeBanner\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Sportpat\HomeBanner\Api\Data\BannerInterface;
use Sportpat\HomeBanner\Api\Data\BannerInterfaceFactory;
use Sportpat\HomeBanner\Api\Data\BannerSearchResultInterfaceFactory;
use Sportpat\HomeBanner\Api\BannerRepositoryInterface;
use Sportpat\HomeBanner\Model\ResourceModel\Banner as BannerResourceModel;
use Sportpat\HomeBanner\Model\ResourceModel\Banner\Collection;
use Sportpat\HomeBanner\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;

class BannerRepository implements BannerRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Banner resource model
     *
     * @var BannerResourceModel
     */
    protected $resource;

    /**
     * Banner collection factory
     *
     * @var BannerCollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * Banner interface factory
     *
     * @var BannerInterfaceFactory
     */
    protected $bannerInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var BannerSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     * @param BannerResourceModel $resource
     * @param BannerCollectionFactory $bannerCollectionFactory
     * @param BannernterfaceFactory $bannerInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param BannerSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        BannerResourceModel $resource,
        BannerCollectionFactory $bannerCollectionFactory,
        BannerInterfaceFactory $bannerInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        BannerSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource             = $resource;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->bannerInterfaceFactory  = $bannerInterfaceFactory;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save Banner.
     *
     * @param \Sportpat\HomeBanner\Api\Data\BannerInterface $banner
     * @return \Sportpat\HomeBanner\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(BannerInterface $banner)
    {
        /** @var BannerInterface|\Magento\Framework\Model\AbstractModel $banner */
        try {
            $this->resource->save($banner);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Banner: %1',
                $exception->getMessage()
            ));
        }
        return $banner;
    }

    /**
     * Retrieve Banner
     *
     * @param int $bannerId
     * @return \Sportpat\HomeBanner\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($bannerId)
    {
        if (!isset($this->instances[$bannerId])) {
            /** @var BannerInterface|\Magento\Framework\Model\AbstractModel $banner */
            $banner = $this->bannerInterfaceFactory->create();
            $this->resource->load($banner, $bannerId);
            if (!$banner->getId()) {
                throw new NoSuchEntityException(__('Requested Banner doesn\'t exist'));
            }
            $this->instances[$bannerId] = $banner;
        }
        return $this->instances[$bannerId];
    }

    /**
     * Retrieve Banners matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Sportpat\HomeBanner\Api\Data\BannerSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Sportpat\HomeBanner\Api\Data\BannerSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Sportpat\HomeBanner\Model\ResourceModel\Banner\Collection $collection */
        $collection = $this->bannerCollectionFactory->create();

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
            $collection->addOrder('main_table.' . BannerInterface::BANNER_ID, SortOrder::SORT_ASC);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var BannerInterface[] $banners */
        $banners = [];
        /** @var \Sportpat\HomeBanner\Model\Banner $banner */
        foreach ($collection as $banner) {
            /** @var BannerInterface $bannerDataObject */
            $bannerDataObject = $this->bannerInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $bannerDataObject,
                $banner->getData(),
                BannerInterface::class
            );
            $banners[] = $bannerDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($banners);
    }

    /**
     * Delete Banner
     *
     * @param BannerInterface $banner
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(BannerInterface $banner)
    {
        /** @var BannerInterface|\Magento\Framework\Model\AbstractModel $banner */
        $id = $banner->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($banner);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to removeBanner %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Banner by ID.
     *
     * @param int $bannerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($bannerId)
    {
        $banner = $this->get($bannerId);
        return $this->delete($banner);
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
