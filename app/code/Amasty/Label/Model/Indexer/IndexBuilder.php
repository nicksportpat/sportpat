<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\Indexer;

use Amasty\Label\Api\Data\LabelIndexInterface;
use Amasty\Label\Helper\Config;
use Amasty\Label\Model\AbstractLabels;
use Amasty\Label\Model\Labels;
use Amasty\Label\Model\ResourceModel\Index;
use Magento\Framework\Exception\LocalizedException;
use Amasty\Label\Model\ResourceModel\Labels\CollectionFactory;
use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Indexer\CacheContext;
use Magento\Framework\Event\ManagerInterface;
use Magento\Catalog\Model\Product;

class IndexBuilder
{
    /**
     * @var \Amasty\Label\Model\ResourceModel\Labels\Collection|null
     */
    private $fullLabelCollection = null;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Index
     */
    private $indexResource;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @var int
     */
    private $batchCount;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var int
     */
    private $batchCacheCount;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Psr\Log\LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        Index $indexResource,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        Config $config,
        CacheContext $cacheContext,
        ManagerInterface $eventManager,
        $batchCount = 1000,
        $batchCacheCount = 100
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->indexResource = $indexResource;
        $this->productRepository = $productRepository;
        $this->batchCount = $batchCount;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->config = $config;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
        $this->batchCacheCount = $batchCacheCount;
    }

    /**
     * Reindex by ids
     *
     * @param array $ids
     * @throws LocalizedException
     * @return void
     * @api
     */
    public function reindexByProductIds(array $ids)
    {
        $this->indexResource->beginTransaction();
        try {
            $this->cleanByProductIds($ids);
            $this->doReindexByProductIds($ids);
            $this->indexResource->commit();
        } catch (\Exception $e) {
            $this->indexResource->rollBack();
            $this->logger->critical($e);
            throw new LocalizedException(
                __("Amasty label indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * Reindex by label ids
     *
     * @param array $ids
     * @throws LocalizedException
     * @return void
     * @api
     */
    public function reindexByLabelIds($ids)
    {
        $this->indexResource->beginTransaction();
        try {
            $this->cleanByLabelIds($ids);
            $this->doReindexByLabelIds($ids);
            $this->indexResource->commit();
        } catch (\Exception $e) {
            $this->indexResource->rollBack();
            $this->logger->critical($e);
            throw new LocalizedException(
                __("Amasty label indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * @param $id
     * @throws LocalizedException
     */
    public function reindexByProductId($id)
    {
        if (!is_array($id)) {
            $id = [$id];
        }

        $this->reindexByProductIds($id);
    }

    /**
     * @param $id
     * @throws LocalizedException
     */
    public function reindexByLabelId($id)
    {
        $this->reindexByLabelIds([$id]);
    }

    /**
     * @param $productIds
     * @throws LocalizedException
     */
    private function cleanByProductIds($productIds)
    {
        $this->indexResource->cleanByProductIds($productIds);
    }

    /**
     * @param $labelIds
     * @throws LocalizedException
     */
    private function cleanByLabelIds($labelIds)
    {
        $this->indexResource->cleanByLabelIds($labelIds);
    }

    /**
     * @param array $ids
     * @return $this
     */
    private function doReindexByProductIds($ids)
    {
        foreach ($this->getFullLabelCollection() as $label) {
            $this->reindexByLabelAndProductIds($label, $ids);
        }

        return $this;
    }

    /**
     * @param array $ids
     * @return $this
     */
    private function doReindexByLabelIds($ids)
    {
        foreach ($this->getLabelCollection($ids) as $label) {
            $this->reindexByLabelAndProductIds($label);
            $this->cacheContext->registerEntities(AbstractLabels::CACHE_TAG, [$label->getId()]);
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
        }

        return $this;
    }

    /**
     * @param Labels $label
     * @param $ids
     * @return $this
     */
    private function reindexByLabelAndProductIds(Labels $label, $ids = null)
    {
        list($rows, $productIds) = $this->prepareData($label, $ids);

        if (!empty($rows)) {
            $this->indexResource->insertIndexData($rows);
        }
        if (!empty($productIds)) {
            $this->cacheContext->registerEntities(Product::CACHE_TAG, $productIds);
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
        }

        return $this;
    }

    /**
     * @param $label
     * @param $ids
     * @return array
     */
    private function prepareData($label, $ids)
    {
        $rows = [];
        $productIds = [];
        $count = 0;
        $matchedProductIds = $label->getLabelMatchingProductIds($ids);
        $labelStoreIds = $label->getStoreIds();
        if ($labelStoreIds && is_array($labelStoreIds) && $matchedProductIds) {
            foreach ($matchedProductIds as $productId => $matchedStores) {
                $stores = array_intersect(array_keys($matchedStores), $labelStoreIds);
                if ($stores) {
                    foreach ($stores as $storeId) {
                        $rows[] = [
                            LabelIndexInterface::PRODUCT_ID => (int) $productId,
                            LabelIndexInterface::LABEL_ID => $label->getId(),
                            LabelIndexInterface::STORE_ID => $storeId
                        ];
                        $count++;
                    }
                    $productIds[] = (int) $productId;

                    if ($count >= $this->batchCount) {
                        $this->indexResource->insertIndexData($rows);
                        $rows  = [];
                        $count = 0;
                    }

                    if (count($productIds) > $this->batchCacheCount) {
                        $this->cacheContext->registerEntities(Product::CACHE_TAG, $productIds);
                        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);

                        $productIds = [];
                    }
                }
            }
        }

        return [$rows, $productIds];
    }

    /**
     * Full reindex
     *
     * @throws LocalizedException
     * @return void
     * @api
     */
    public function reindexFull()
    {
        $this->indexResource->beginTransaction();
        try {
            $this->doReindexFull();
            $this->indexResource->commit();
        } catch (\Exception $e) {
            $this->indexResource->rollBack();
            $this->logger->critical($e->getMessage());
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }

    /**
     * @param null $labelIds
     * @return mixed
     */
    private function getLabelCollection($labelIds = null)
    {
        $collection = $this->collectionFactory->create()->addActiveFilter();
        if ($labelIds) {
            $collection->addFieldToFilter(LabelInterface::LABEL_ID, ['in' => $labelIds]);
        }

        return $collection;
    }

    /**
     * @return mixed
     */
    private function getFullLabelCollection()
    {
        if ($this->fullLabelCollection === null) {
            $this->fullLabelCollection = $this->collectionFactory->create()
                ->addActiveFilter();
        }

        return $this->fullLabelCollection;
    }

    /**
     * @return $this
     */
    private function doReindexFull()
    {
        $this->indexResource->cleanAllIndex();

        foreach ($this->getFullLabelCollection() as $label) {
            $this->reindexByLabelAndProductIds($label);
        }

        return $this;
    }

    /**
     * @return array
     */
    private function getAllProductIds()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();

        return $collection->getAllIds();
    }
}
