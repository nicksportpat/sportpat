<?php

namespace Auctane\Api\Block\System\Config;
 
use Magento\Framework\App\Config\ScopeConfigInterface;
 
class Button extends \Magento\Config\Block\System\Config\Form\Field
{
    
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

        return '<button id="btnid" class="action-default scalable save primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
         onclick="javascript:generate(); return false;"
         >Generate Key</button>'."<script type='text/javascript'>

            function generate(){
                var rString = randomString(16, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
                document.getElementById('shipstation_general_shipstation_ship_api_key').value = rString;
            }

            function randomString(length, chars) {
            var result = '';
            for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
            return result;
            }
        </script>";

    }
}
