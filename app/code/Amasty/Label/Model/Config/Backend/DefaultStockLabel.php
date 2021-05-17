<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\Config\Backend;

/**
 * Class DefaultStockLabel
 * @package Amasty\Label\Model\Config\Backend
 */
class DefaultStockLabel extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        if ($this->isValueChanged()) {
            $id = $this->getOldValue();
            $this->getData('config')->changeStatus($id, 0);
        }
        $id = $this->getValue();
        $this->getData('config')->changeStatus($id, 1);

        return parent::beforeSave();
    }
}
