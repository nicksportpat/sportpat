<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Controller\Adminhtml\Labels;

use Exception;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Amasty\Label\Model\Labels;
use Magento\Framework\Exception\LocalizedException;
use RuntimeException;
use Amasty\Label\Api\Data\LabelInterface;

/**
 * Class InlineEdit
 * @package Amasty\Label\Controller\Adminhtml\Labels
 */
class InlineEdit extends \Amasty\Label\Controller\Adminhtml\Labels
{
    /**
     * Inline edit action
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if ($this->getRequest()->getParam('isAjax') && !empty($postItems)) {
            foreach ($postItems as $labelId => $labelData) {
                /** @var Labels $label */
                $label = $this->labelRepository->getById($labelId);
                try {
                    $this->processData($label, $labelData);
                    $this->labelRepository->save($label);
                } catch (LocalizedException $e) {
                    $messages[] = $e->getMessage();
                    $error = true;
                } catch (RuntimeException $e) {
                    $messages[] = $e->getMessage();
                    $error = true;
                } catch (Exception $e) {
                    $messages[] = __('Something went wrong while saving the label.');
                    $error = true;
                }
            }
        } else {
            $messages[] = __('Please correct the data sent.');
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Prepare label before saving
     *
     * @param Labels $label
     * @param array $labelData
     */
    private function processData(Labels $label, array $labelData)
    {
        if (isset($labelData[LabelInterface::NAME])) {
            $label->setName($labelData[LabelInterface::NAME]);
        }
        if (isset($labelData[LabelInterface::PROD_POS])) {
            $label->setProdPos((int)$labelData[LabelInterface::PROD_POS]);
        }
        if (isset($labelData[LabelInterface::CAT_POS])) {
            $label->setCatPos((int)$labelData[LabelInterface::CAT_POS]);
        }
        if (isset($labelData[LabelInterface::STATUS])) {
            $label->setStatus((int)$labelData[LabelInterface::STATUS]);
        }
    }
}
