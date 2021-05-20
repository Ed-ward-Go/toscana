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
                return CreditManagment.getAvailableCredit();
            },
            canPayOrder: function () {
                return CreditManagment.canPayOrder();
            },
            getNotPayMessage: function () {
                return $t('You have exceded your credit.');
            },
            isActionToolbarVisible: function () {
                return this.canPayOrder();
            }
        });
    }
);
