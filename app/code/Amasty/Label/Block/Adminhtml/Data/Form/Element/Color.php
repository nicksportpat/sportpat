<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Label\Block\Adminhtml\Data\Form\Element;

/**
 * Class Color
 * @package Amasty\Label\Block\Adminhtml\Data\Form\Element
 */
class Color extends \Magento\Framework\Data\Form\Element\Text
{
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        $value = '#' . $this->getValue();
        $html .= '<script type="text/javascript">
            require( [
                "jquery",
                "jquery/colorpicker/js/colorpicker"
            ] , function ($) {
                    var elemelt = $("#' . $this->getHtmlId() . '");
                    elemelt.css("backgroundColor", "'. $value .'");

                    elemelt.ColorPicker({
                        color: "'. $value .'",
                        onChange: function (hsb, hex, rgb) {
                            elemelt.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    });
            });
        </script>';

        return $html;
    }
}
