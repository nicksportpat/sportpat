/*
 * Copyright © 2019 Aitoc. All rights reserved.
 */
define([
    'Magento_Ui/js/form/element/select',
    'uiRegistry',
    'underscore'
], function (Select, uiRegistry, _) {
    'use strict';

    return Select.extend({
        initialize: function () {
            this._super();
            var self = this;

            setTimeout(function(){self.toogleDependableFields(self.value());}, 2000);

        },

        onUpdate: function (value) {
            this.toogleDependableFields(value);
            return this._super();
        },

        toogleDependableFields: function (value) {
            var discountPercentField = uiRegistry.get('index = discount_percent');
            var discountPeriodField = uiRegistry.get('index = discount_period');
            var discountSalesRule = uiRegistry.get('index = sales_rule_id');
            var discountTypeRule = uiRegistry.get('index = discount_type');

            if (value == '0') {
                discountPercentField.hide();
                discountPeriodField.hide();
                discountSalesRule.hide();
                discountTypeRule.hide();
            }
            if (value == '1') {
                discountPercentField.show();
                discountPeriodField.show();
                discountTypeRule.show();
            }
        }
    });
});
