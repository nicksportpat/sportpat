<?php
namespace Sportpat\OrderSync\Block\Adminhtml\Sales;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\AuthorizationInterface;


class ViewOrderInLS implements ButtonProviderInterface {

    private $authorization;

    private $context;


    public function __construct(
        AuthorizationInterface $authorization,
    Context $context
    ) {

        $this->authorization = $authorization;
        $this->context = $context;

    }

    public function getButtonData() {

        if(!$this->authorization->isAllowed('Magento_Cms::save')) {
            return [];
        }

        return [
            'label' => __('View LS Sync Queue'),
            'on_click' => sprintf("location.href='%s';", $this->getLSOrderURL()),
            'class' => 'primary',
            'sort_order' => 999
        ];

    }

    public function getLSOrderURL() {

        return $this->context->getUrlBuilder()->getUrl('sportpat_ordersync/syncedorder/index', []);

    }

}