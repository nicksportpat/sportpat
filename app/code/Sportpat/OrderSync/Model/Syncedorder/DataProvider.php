<?php
namespace Sportpat\OrderSync\Model\Syncedorder;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Sportpat\OrderSync\Model\ResourceModel\Syncedorder\CollectionFactory as SyncedorderCollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * Loaded data cache
     *
     * @var array
     */
    protected $loadedData;

    /**
     * Data persistor
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param SyncedorderCollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        SyncedorderCollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Sportpat\OrderSync\Model\Syncedorder $syncedorder */
        foreach ($items as $syncedorder) {
            $this->loadedData[$syncedorder->getId()] = $syncedorder->getData();
        }
        $data = $this->dataPersistor->get('sportpat_order_sync_syncedorder');
        if (!empty($data)) {
            $syncedorder = $this->collection->getNewEmptyItem();
            $syncedorder->setData($data);
            $this->loadedData[$syncedorder->getId()] = $syncedorder->getData();
            $this->dataPersistor->clear('sportpat_order_sync_syncedorder');
        }
        return $this->loadedData;
    }
}
