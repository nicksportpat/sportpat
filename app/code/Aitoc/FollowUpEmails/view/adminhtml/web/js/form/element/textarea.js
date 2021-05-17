/*
 * Copyright © 2019 Aitoc. All rights reserved.
 */

define([
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry',
    'underscore',
    "jquery"
], function (Textarea, uiRegistry, _, $) {
    'use strict';

    return Textarea.extend({
        defaults: {
            cols: 15,
            rows: 20,
            elementTmpl: 'ui/form/element/textarea',
            valueUpdate: 'input'
        },

        onUpdate: function (value) {
            this.saveSettignsSelect = uiRegistry.get('index = save_settings');
            this.newTemplateName = uiRegistry.get('index = new_template_name');

            //TO DO : disable select only if current value is different from initial
            // var t = uiRegistry.get('index = template_content');
            // console.log(t.hasChanged());
            if (this.saveSettignsSelect.value() === 'save_as_new') {
                this.newTemplateName.disabled(false);
            }
            this.saveSettignsSelect.disabled(false);
            return this._super();
        }
    });
});
