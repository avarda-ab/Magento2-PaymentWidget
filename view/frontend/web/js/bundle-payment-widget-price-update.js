define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.priceBundle', widget, {
            _create: function () {
                $(this.options.priceBoxSelector, this.element).on('priceUpdated', function (event, prices) {
                    const paymentWidget = $('#avarda-payment-widget');

                    if (paymentWidget.length && prices && prices.finalPrice) {
                        paymentWidget.attr('price', prices.finalPrice.amount);
                    }
                });

                this._super();
            }
        });

        return $.mage.priceBundle;
    }
});
