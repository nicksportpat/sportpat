<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\Config\Backend;

/**
 * Class StockStatus
 * @package Amasty\Label\Model\Config\Backend
 */
class StockStatus extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        if ($this->isValueChanged()) {
            $id = $this->getData('config')->getModuleConfig('stock_status/default_label');
            $status = $this->getValue();
            $this->getData('config')->changeStatus($id, $status);
        }

        return parent::beforeSave();
    }
}
