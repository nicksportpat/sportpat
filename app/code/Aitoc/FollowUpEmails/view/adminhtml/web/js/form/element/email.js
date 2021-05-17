/*
 * Copyright © 2019 Aitoc. All rights reserved.
 */
define([
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry',
    'underscore',
    "jquery"
], function (Select, uiRegistry, _, $) {
    'use strict';

    return Select.extend({
        defaults: {
            placeholder: 'johndoe@domain.com'
        },
        initialize: function () {
            this._super();
        },

        onUpdate: function () {
           return true;
        },

        sendEmail : function (input) {
            input.validation['validate-email'] = true;
            input.validation['validate-no-empty'] = true;
            input.validate();
            if(input.error()) {
                input.validation = [];
                return false;
            }
            var textArea = $("textarea[name='template_content']");
            var content = textArea[0].value;
            var campaignIdInput =  $("input[name='campaign_id']");
            var campaignID = campaignIdInput[0].value;
            var inputUrl =  $("input[name='ajax_links']");
            var ajaxLinks = JSON.parse(inputUrl[0].value);
            var url = ajaxLinks['test_email'] + 'form_key/' + FORM_KEY;
            $.ajax({
                showLoader: true,
                url: url,
                data: { email : input.value(), content : content, 'campaign_id' : campaignID },
                type: 'POST',
                dataType: 'json',
                success: function(data, status, xhr) {
                    var sendButton = document.getElementsByClassName('send_email_button');
                    var messageContainer = document.createElement("div");
                    messageContainer.classList.add("message_container_success");
                    messageContainer.innerText = "Email was successfully sent";
                    sendButton[0].after(messageContainer);
                    messageContainer = $('.message_container_success');
                    messageContainer.fadeOut(10000 , function () {
                        messageContainer.remove();
                    });

                },
                error: function (xhr, status, errorThrown) {
                    var sendButton = document.getElementsByClassName('send_email_button');
                    var messageContainer = document.createElement("div");
                    messageContainer.classList.add("message_container_error");
                    messageContainer.innerText = "Email wasn't sent. Something went wrong";
                    sendButton[0].after(messageContainer);
                    messageContainer = $('.message_container_error');
                    messageContainer.fadeOut(10000 , function () {
                        messageContainer.remove();
                    });

                    console.log('Error happens. Try again.');
                    console.log(errorThrown);
                }
            });

            input.validation = [];
        }

    });
});
