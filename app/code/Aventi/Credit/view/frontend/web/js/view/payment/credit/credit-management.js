/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Catalog/js/price-utils'
], function (priceUtils) {
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
         * Format price
         *
         * @return {String}
         */
        formatPrice: function(price) {
            return priceUtils.formatPrice(price, priceFormat);
        }
    }
});
