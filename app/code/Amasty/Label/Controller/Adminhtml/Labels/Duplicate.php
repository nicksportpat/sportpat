<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Controller\Adminhtml\Labels;

/**
 * Class Duplicate
 * @package Amasty\Label\Controller\Adminhtml\Labels
 */
class Duplicate extends \Amasty\Label\Controller\Adminhtml\Labels
{
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->labelRepository->duplicateLabel($id);
                $this->messageManager->addSuccessMessage(__('You have duplicated the label.'));

                $this->_redirect('amasty_label/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t duplicate item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('amasty_label/*/edit', ['id' =>  $id]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a item to duplicate.'));

        $this->_redirect('amasty_label/*/');
    }
}
