
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, uiRegistry, select) {
    'use strict';

    return select.extend({
        dependentFieldNames: [
            'index = discount_percent',
            'index = discount_period',
            'index = sales_rule_id',
        ],

        dependentFields : [],

        initialize: function() {
            this._super();
            uiRegistry.promise(this.dependentFieldNames).done(_.bind(function() {
                this.dependentFields = arguments;
            }, this));

            var self = this;

            this.proceedVisibility(this.value());
            setTimeout(function(){self.proceedVisibility(self.value());}, 2000);
        },

        /**
         * Hide fields on coupon tab
         */
        onUpdate: function (value) {
            this.proceedVisibility(value);
            return this._super();
        },

        /**
         *
         * @param value
         */
        proceedVisibility: function (value) {
            $.each(this.dependentFields, function () {
                if (typeof this.show === 'function') {
                    if (this.visibleValue == 'sales_rule_id' && value == 2) {
                        this.show();
                    } else if ((this.visibleValue == 'discount_percent' || this.visibleValue == 'discount_period')
                        && (value == 0 || value == 1)
                    ) {
                        this.show();
                    } else {
                        this.hide();
                    }
                }
            });
        }
    });
});
