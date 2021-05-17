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
        },

        onUpdate: function (value) {
            this.toogleDependableFields(value);
            return this._super();
        },

        toogleDependableFields: function (value) {
            var newTemplateNameField = uiRegistry.get('index = new_template_name');

            if (value == 'overwrite_original') {
                newTemplateNameField.disabled(true);
            }
            if (value == 'save_as_new') {
                newTemplateNameField.disabled(false);
            }
        }
    });
});
