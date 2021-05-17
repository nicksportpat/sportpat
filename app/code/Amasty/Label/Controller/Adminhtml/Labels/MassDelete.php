<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Controller\Adminhtml\Labels;

/**
 * Class MassDelete
 */
class MassDelete extends MassActionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        return __('A total of %1 record(s) have been deleted.', $collectionSize);
    }

    /**
     * {@inheritdoc}
     */
    protected function itemAction($label)
    {
        $label->delete();
    }
}
