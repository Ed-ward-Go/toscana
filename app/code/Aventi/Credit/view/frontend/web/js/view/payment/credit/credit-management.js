/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Catalog/js/price-utils',
    'Aventi_Credit/js/view/payment/credit/check-pay-order'
], function (priceUtils, checkPayOrder) {
    "use strict";

    /**
     * Checkout configuration data
     */
    var paymentData = window.checkoutConfig.payment.credit,
        priceFormat = window.checkoutConfig.priceFormat;

    return {

        /**
         * Get customer available credit balance
         *
         * @return {Number}
         */
        getAvailableCreditPure: function () {
            return paymentData.available;
        },

        /**
         * Get customer available credit balance formatted
         *
         * @return {String}
         */
        getAvailableCredit: function () {
            return this.formatPrice(this.getAvailableCreditPure());
        },

        /**
         *
         * @returns {*}
         */
        canPayOrder: function(){
            return checkPayOrder(paymentData);
        },

        /**
         * Format price
         *
         * @return {String}
         */
        formatPrice: function(price) {
            return priceUtils.formatPrice(price, priceFormat);
        }
    }
});
