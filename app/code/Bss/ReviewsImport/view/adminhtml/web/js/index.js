/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ReviewsImport
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

require([
    'jquery'
], function ($) {

    $(document).ready(function (e) {
        var checkData = false;

        $(document).on("change", "#file", function () {
            checkData = false;
            $('#import').val('Check Data');
            if ($('#file').val().length>0) {
                $('#import').removeAttr('disabled');
            } else {
                $('#import').attr('disabled','disabled');
            }
        });

        $("#import-form").on('submit',(function(e) {
            if (checkData === false) {
                e.preventDefault();
                $.ajax({
                    url: $('#validate-url').val(),
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    showLoader: true,
                    success: function (data) {
                        var messages = '', html;
                        $.each(data, function(k, v) {
                            if (k == 'success') {
                                $.each(v, function(key, message) {
                                    html = '<div class="message message-success success">' + message + '</div>';
                                    messages += html;
                                });
                                checkData = true;
                                $('#import').val('Import Product Reviews');
                            }
                            if (k == 'error') {
                                $.each(v, function(key, message) {
                                    html = '<div class="message message-error error">' + message + '</div>';
                                    messages += html;
                                });
                            }
                        });
                        $('#validate-message .messages').html(messages);
                    },
                    error: function (request, status, error) {
                        console.log(error);
                    }
                });
            }
        }));
    });
});
