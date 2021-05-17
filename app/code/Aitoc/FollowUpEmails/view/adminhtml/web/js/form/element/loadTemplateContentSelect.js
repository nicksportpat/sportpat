/*
 * Copyright © 2019 Aitoc. All rights reserved.
 */
define([
    'Magento_Ui/js/form/element/select',
    'uiRegistry',
    'underscore',
    "jquery"
], function (Select, uiRegistry, _, $) {
    'use strict';

    return Select.extend({
        initialize: function () {
            this._super();
        },

        onUpdate: function (value) {
            var inputUrl =  $("input[name='ajax_links']");
            var ajaxLinks = JSON.parse(inputUrl[0].value);
            var url = ajaxLinks['update_content'] + 'form_key/' + FORM_KEY;
            $.ajax({
                showLoader: true,
                url: url,
                data: { template_id : value },
                type: 'POST',
                dataType: 'json',
                success: function(data, status, xhr) {
                    if(!data) {
                        return;
                    }

                    var templateContentTextArea = $('textarea[name="template_content"]');
                    templateContentTextArea[0].value = data.templateContent;
                    templateContentTextArea.change();

                    var templateSubjectText = $('input[name="template_subject"]');
                    templateSubjectText.val(data.templateSubject);
                    templateSubjectText.change();
                },

                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                    console.log(errorThrown);
                }
            });
            return this._super();
        }
    });
});
