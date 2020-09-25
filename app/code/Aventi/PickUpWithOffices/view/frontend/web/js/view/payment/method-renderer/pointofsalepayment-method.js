define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Aventi_PickUpWithOffices/payment/pointofsalepayment'
            },
            getMailingAddress: function () {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },
            getInstructions: function () {
                //return window.checkoutConfig.payment.instructions[this.item.method];
                return window.checkoutConfig.payment.instructions[this.item.method].replace(/&lt;/g, '<').replace(/&gt;/g, '>') ;
            },
        });
    }
);
