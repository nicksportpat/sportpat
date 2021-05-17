<?php
namespace Sportpat\OrderSync\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class SyncedorderActions extends Column
{
    /**
     * Url path  to edit
     * @var string
     */
    const URL_PATH_EDIT = 'sportpat_ordersync/syncedorder/edit';

    /**
     * Url path  to delete
     * @var string
     */
    const URL_PATH_DELETE = 'sportpat_ordersync/syncedorder/delete';

    /**
     * Url builder
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * constructor
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['syncedorder_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'syncedorder_id' => $item['syncedorder_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'syncedorder_id' => $item['syncedorder_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.magento_orderid }"'),
                                'message' => __('Are you sure you want to delete the Synced Order "${ $.$data.magento_orderid }" ?')
                            ]
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
