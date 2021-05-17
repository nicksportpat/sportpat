/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
	'mage/url',
    'jquery/ui',
	'mage/cookies',
    'Magento_Catalog/js/catalog-add-to-cart'
], function($, $t, url) {
    "use strict";

    $.widget('scommerce.catalogAddToCart', $.mage.catalogAddToCart, {
        gaAddToCart: function($){
			var list = $.mage.cookies.get('productlist');
			//console.log(list);
			$.ajax({
                url: url.build('universalanalytics/index/addtocart'),
                type: 'get',
                dataType: 'json',
                success: function(productToBasket) {
					if (productToBasket != undefined) {
						if (list == undefined){
							list='Category - '+ productToBasket.category
						}
						//manipulationOfCart(productToBasket, 'add', list);
						$.mage.cookies.clear("productlist");
					}
					$.ajax({
						url: url.build('universalanalytics/index/unsaddtocart'),
						type: 'get',
						success: function(response) {
						}
					});
				}
			})
        },
        ajaxSubmit: function(form) {
            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function(res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                        
                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    self.enableAddToCartButton(form);
                    self.gaAddToCart($);
                }

            });
        }
    });

    return $.scommerce.catalogAddToCart;
});