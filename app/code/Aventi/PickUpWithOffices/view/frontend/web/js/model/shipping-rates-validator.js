define(
    [
        'jquery',
        'mageUtils',

        './shipping-rates-validation-rules',
        'mage/translate'
    ],
    function ($, utils, validationRules, $t) {
        'use strict';
        return {
            validationErrors: [],
            validate: function(address) {

                console.log('Ingresa');

                var shipping = $('.checkout-shipping-method input[class="radio"]:checked').val() || '';
                var self = this;
                this.validationErrors = [];
                if($('.js-click.active').length <= 0 && shipping == 'pickup_pickup'){
                    self.validationErrors.push('Por favor seleccione una de nuestras oficinas');
                }
                return !Boolean(this.validationErrors.length);
            }
        };
    }
);
