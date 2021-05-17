<?php
namespace Sportpat\OrderSync\Block\Adminhtml\Button\Syncedorder;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete implements ButtonProviderInterface
{
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Delete constructor.
     * @param Registry $registry
     * @param UrlInterface $url
     */
    public function __construct(Registry $registry, UrlInterface $url)
    {
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getSyncedorderId()) {
            $data = [
                'label' => __('Delete Synced Order'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return \Sportpat\OrderSync\Api\Data\SyncedorderInterface | null
     */
    private function getSyncedorder()
    {
        return $this->registry->registry('current_syncedorder');
    }

    /**
     * @return int|null
     */
    private function getSyncedorderId()
    {
        $syncedorder = $this->getSyncedorder();
        return ($syncedorder) ? $syncedorder->getId() : null;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->url->getUrl(
            '*/*/delete',
            [
                'syncedorder_id' => $this->getsyncedorderId()
            ]
        );
    }
}
