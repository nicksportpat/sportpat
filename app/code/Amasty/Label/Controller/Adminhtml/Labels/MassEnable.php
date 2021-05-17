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
class MassEnable extends MassActionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function itemAction($label)
    {
        $label->setStatus(1);
        $label->save();
    }
}
