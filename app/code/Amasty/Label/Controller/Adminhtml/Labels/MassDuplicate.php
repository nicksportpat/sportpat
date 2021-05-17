<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Controller\Adminhtml\Labels;

/**
 * Class MassDuplicate
 * @package Amasty\Label\Controller\Adminhtml\Labels
 */
class MassDuplicate extends MassActionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function itemAction($label)
    {
        $this->labelRepository->duplicateLabel($label->getId());
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        return __('A total of %1 record(s) have been duplicated.', $collectionSize);
    }
}
