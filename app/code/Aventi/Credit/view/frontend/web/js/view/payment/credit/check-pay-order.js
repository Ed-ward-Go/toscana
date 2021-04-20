/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Checkout/js/model/quote',
    'underscore'
], function (quote, _) {
    "use strict";

    return function (paymentData, checkoutTotal) {
        var baseGrandTotal = !_.isUndefined(checkoutTotal)
            ? checkoutTotal
            : quote.totals()['base_grand_total'];
        return paymentData.available >= baseGrandTotal;
    }
});
