/*
 * Copyright © 2019 Aitoc. All rights reserved.
 */
define([
    'Magento_Ui/js/grid/listing'
], function (Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aitoc_FollowUpEmails/events/listing'
        }
    });
});
