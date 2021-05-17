<?php
namespace Sportpat\OrderSync\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Sales\Api\OrderRepositoryInterface;

class LsAction extends Column
{

    protected $url;
    protected $urlBuilder;
    protected $db;
    protected $_orderRepository;

    public function __construct(

        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Framework\App\ResourceConnection $resource,
        OrderRepositoryInterface $orderRepository,
        array $components = [],
        array $data = []

    )
    {

        $this->urlBuilder = $urlBuilder;
        $this->db = $resource->getConnection();
        $this->_orderRepository = $orderRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);

    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as & $item) {

                $order = $this->_orderRepository->get($item["entity_id"]);
                $incrementId = $order->getIncrementId();
                $sql = "SELECT * FROM sportpat_order_sync_syncedorder WHERE magento_orderid = '" . $order->getIncrementId() . "'";
                $rows = $this->db->fetchAll($sql);

                $label = "<span style='color:red'>NOT SYNCED</span>";

                if (!empty($rows)) {
                    if ($rows[0]["status"] == 1) {
                        $label = "<span ><img src='/media/pending.png' width='40' /></span>";
                    }
                    if ($rows[0]["status"] == 2) {
                        $label = "<a href='https://us.lightspeedapp.com/?name=transaction.views.transaction&form_name=view&id=" . $rows[0]["lightspeed_orderid"] . "&tab=details' onclick='windows.location.href=\"https://us.lightspeedapp.com/?name=transaction.views.transaction&form_name=view&id=" . $rows[0]["lightspeed_orderid"] . "&tab=details\"' target='_blank' style='color:darkgreen; font-weight:700;'><img src='/media/syncedok.png' width='40' /></a>";
                    }
                    if ($rows[0]["status"] == 3) {
                        $label = "<span ><img src='/media/error.png' width='40' /></span>";
                    }
                    if ($rows[0]["status"] == 4) {
                        $label = "<span ><img src='/media/processing.png' width='40' /></span>";
                    }








                } else {
                    $label = "<span ><img src='/media/pending.png' width='40' /></span>";
                }
                $item[$this->getData('name')] = $label;
            }
            return $dataSource;
        }


    }
}

/*
 * $item[$this->getData('name')] = [
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
 */