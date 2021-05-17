<?php
namespace Sportpat\Tabcontent\Model\Tabcontent;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Sportpat\Tabcontent\Model\ResourceModel\Tabcontent\CollectionFactory as TabcontentCollectionFactory;

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
     * @param TabcontentCollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        TabcontentCollectionFactory $collectionFactory,
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
        /** @var \Sportpat\Tabcontent\Model\Tabcontent $tabcontent */
        foreach ($items as $tabcontent) {
            $this->loadedData[$tabcontent->getId()] = $tabcontent->getData();
            if (isset($this->loadedData[$tabcontent->getId()]['image'])) {
                $image = [];
                $image[0]['name'] = $tabcontent->getImage();
                $image[0]['url'] = $tabcontent->getImageUrl();
                $this->loadedData[$tabcontent->getId()]['image'] = $image;
            }
        }
        $data = $this->dataPersistor->get('sportpat_tabcontent_tabcontent');
        if (!empty($data)) {
            $tabcontent = $this->collection->getNewEmptyItem();
            $tabcontent->setData($data);
            $this->loadedData[$tabcontent->getId()] = $tabcontent->getData();
            $this->dataPersistor->clear('sportpat_tabcontent_tabcontent');
        }
        return $this->loadedData;
    }
}
