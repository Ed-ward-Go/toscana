define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/translate',
        'Aventi_Credit/js/view/payment/credit/credit-management',
    ],
    function (Component, $t, CreditManagment) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Aventi_Credit/payment/credit'
            },
            getMailingAddress: function () {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },
            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            },
            getAvailableCreditBalance: function () {
                console.log(window.checkoutConfig.payment);
                return $t('Disponible: ') + CreditManagment.getAvailableCredit();
            },
            canPayOrder: function () {
                return false;
            },
            getNotPayMessage: function () {
                return $t('Insufficient credit funds');
            },
            isActionToolbarVisible: function () {
                return this.canPayOrder();
            }
        });
    }
);
