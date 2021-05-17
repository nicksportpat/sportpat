/*
 * Copyright © 2019 Aitoc. All rights reserved.
 */
define([
    'Magento_Ui/js/form/components/button',
    'uiRegistry',
    'underscore',
    "jquery"
], function (Button, uiRegistry, _, $) {
    'use strict';

    return Button.extend({
        initialize: function () {
            this._super();
        },

        previewTemplate : function () {
            var textArea = $("textarea[name='template_content']");
            $('#preview_text')[0].value = textArea[0].value;
            $('#email_template_preview_form')[0].submit();
        }
    });
});
