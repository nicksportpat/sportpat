<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Controller\Adminhtml\Labels;

/**
 * Class Edit
 * @package Amasty\Label\Controller\Adminhtml\Labels
 */
class Edit extends \Amasty\Label\Controller\Adminhtml\Labels
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->labelRepository->getModelLabel();

        if ($id) {
            $model = $this->labelRepository->getById($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This item no longer exists.'));
                $this->_redirect('amasty_label/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->coreRegistry->register('current_amasty_label', $model);
        $this->_initAction();

        // set title and breadcrumbs
        $title = $id ? __('Edit Product Label') : __('New Product Label');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Catalog'), __('Catalog'))
            ->addBreadcrumb(__('Manage Product Labels'), __('Manage Product Labels'));
        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
        }
        $resultPage->getConfig()->getTitle()->prepend(__('Product Labels'));
        $resultPage->getConfig()->getTitle()->prepend($id ? $model->getName() : __('New Product Label'));

        $this->_view->renderLayout();
    }
}
