/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global confirm:true*/
define([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
	'mage/url',
    'jquery/ui',
    'mage/decorate',
    'mage/collapsible',
    'mage/cookies',
	'Magento_Checkout/js/sidebar'
], function ($, authenticationPopup, customerData, alert, confirm, url) {

    $.widget('scommerce.sidebar', $.mage.sidebar, {
        _gaRemoveFromCart: function($){			
			$.ajax({
                url: url.build('universalanalytics/index/removefromcart'),
                type: 'get',
                dataType: 'json',
                success: function(productOutBasket) {
					if (productOutBasket != undefined) {
						manipulationOfCart(productOutBasket, 'remove', '');
					}
					
					$.ajax({
						url: url.build('universalanalytics/index/unsremovefromcart'),
						type: 'get',
						success: function(response) {
						}
					});
				}
			})
        },

        /**
         * Update content after item remove
         *
         * @param elem
         * @param response
         * @private
         */
        _removeItemAfter: function(elem, response) {
            this._gaRemoveFromCart($);
            //console.log('Item has been deleted');
        },
		
		/**
         * Calculate height of minicart list
         *
         * @private
         */
        _calcHeight: function () {
            var self = this,
                height = 0,
                counter = this.options.minicart.maxItemsVisible,
                target = $(this.options.minicart.list),
                outerHeight;

            self.scrollHeight = 0;
            target.children().each(function () {

                if ($(this).find('.options').length > 0) {
                    $(this).collapsible();
                }
                outerHeight = $(this).outerHeight();

                if (counter-- > 0) {
                    height += outerHeight;
                }
                self.scrollHeight += outerHeight;
            });
			if (height>0){
				target.parent().height(height);
			}
        }
		
    });

    return $.scommerce.sidebar;
});
