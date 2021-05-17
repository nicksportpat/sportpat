define([
    'jquery',
    'Amasty_Label/js/configurable/reload',
    'Amasty_Label/js/initLabel'
], function ($, reload) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _loadMedia: function () {
                this.loadLabels();

                return this._super();
            },

            _LoadProductMedia: function () {
                this.loadLabels();

                return this._super();
            },

            loadLabels: function () {
                var productIds = this._CalcProducts(),
                    imageContainer = null;

                if (this.inProductList) {
                    imageContainer = this.element.closest('li.item').find(this.options.jsonConfig['label_category']);
                } else {
                    imageContainer = this.element.closest('.column.main').find(this.options.jsonConfig['label_product']);
                }

                imageContainer.find('.amasty-label-container').addClass('amlabel-swatch').hide();

                if (productIds.length == 0) {
                    productIds.push(this.options.jsonConfig['original_product_id']);
                }

                if (typeof this.options.jsonConfig['label_reload'] != 'undefined') {
                    reload(
                        imageContainer,
                        productIds[0],
                        this.options.jsonConfig['label_reload'],
                        this.inProductList ? 1 : 0,
                        this.options.jsonConfig['original_product_id']
                    );
                }
            }
        });

        return $.mage.SwatchRenderer;
    }
});
