<?php
namespace Sportpat\Tabcontent\Block\Adminhtml\Button\Tabcontent;

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
        if ($this->getTabcontentId()) {
            $data = [
                'label' => __('Delete Manage Content'),
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
     * @return \Sportpat\Tabcontent\Api\Data\TabcontentInterface | null
     */
    private function getTabcontent()
    {
        return $this->registry->registry('current_tabcontent');
    }

    /**
     * @return int|null
     */
    private function getTabcontentId()
    {
        $tabcontent = $this->getTabcontent();
        return ($tabcontent) ? $tabcontent->getId() : null;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->url->getUrl(
            '*/*/delete',
            [
                'tabcontent_id' => $this->gettabcontentId()
            ]
        );
    }
}
