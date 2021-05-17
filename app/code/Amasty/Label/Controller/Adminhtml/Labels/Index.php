<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Controller\Adminhtml\Labels;

/**
 * Class Index
 * @package Amasty\Label\Controller\Adminhtml\Labels
 */
class Index extends \Amasty\Label\Controller\Adminhtml\Labels
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_Label::label');
        $resultPage->getConfig()->getTitle()->prepend(__('Amasty Product Labels'));
        $resultPage->addBreadcrumb(__('Amasty'), __('Amasty'));
        $resultPage->addBreadcrumb(__('Product Labels'), __('Product Labels'));
        return $resultPage;
    }
}
