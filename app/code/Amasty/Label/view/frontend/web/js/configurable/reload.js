define([
    'jquery'
], function ($) {
    return function (imageContainer, productId, reloadUrl, inProductList, originalProductId) {
        if (!this.labels) {
            this.labels = [];
        }

        if (this.labels.indexOf(productId) === -1 && productId != originalProductId) {
            this.labels.push(productId);
            $.ajax({
                url: reloadUrl,
                data: {
                    product_id: productId,
                    in_product_list: inProductList
                },
                method: 'GET',
                cache: true,
                dataType: 'json',
                showLoader: false
            }).done(function (data) {
                if (data.labels) {
                    imageContainer.last().after(data.labels).trigger('contentUpdated');
                }
            });
        }

        imageContainer.find('.amasty-label-for-' + productId).show();
    }
});
