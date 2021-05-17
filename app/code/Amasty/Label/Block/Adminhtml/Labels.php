<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Label\Block\Adminhtml;

/**
 * Class Labels
 * @package Amasty\Label\Block\Adminhtml
 */
class Labels extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'labels';
        $this->_headerText = __('Product Labels');
        $this->_addButtonLabel = __('Add New label');
        parent::_construct();
    }
}
