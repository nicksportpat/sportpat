/*
 * Copyright © 2019 Aitoc. All rights reserved.
 */

define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    var formSelector = '#aitoc-fue-unsubscribe';
    var messageSelector = formSelector + ' .block-title-message';
    var unsubscribeFromAllSelector = '#unsubscribe_all';

    $(function () {
        $(formSelector).submit(sendUnsubscribeFormByAjax);

        $(unsubscribeFromAllSelector).click(checkAllCheckboxes);
    });

    function checkAllCheckboxes() {
        var $form = $(this).closest('form');
        var $checboxes = $form.find('input[type="checkbox"]');

        $checboxes.each(function () {
            $(this).prop('checked', true);
        });
    }

    function sendUnsubscribeFormByAjax(event) {
        event.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var values = form.serializeArray();

        sendByAjax(url, values);
    }

    function sendByAjax(url, values) {
        $.ajax({
            url: url,
            dataType: 'html',
            data: values
        }).done(function () {
            $(messageSelector).text('You unsubscribed!');
        }).fail(function () {
            $(messageSelector).text('Error happen while unsubscribe! Please, try again later');
        });
    }
});

