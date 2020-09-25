define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Aventi_PickUpWithOffices/js/model/offices-validator'
    ],
    function (Component, additionalValidators, officeValidator) {
        'use strict';
        additionalValidators.registerValidator(officeValidator);
        return Component.extend({});
    }
);
