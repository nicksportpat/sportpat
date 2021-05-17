/*
 * Copyright © 2019 Aitoc. All rights reserved.
 */
define([
    'Magento_Ui/js/grid/columns/actions',
    'jquery',
], function (Column, $) {
    'use strict';
 
    return Column.extend({
        defaults: {
            bodyTmpl: 'Aitoc_FollowUpEmails/events/cells/emails',
            emailActions: 'Aitoc_FollowUpEmails/events/cells/emailActions'
        }
    });
});
