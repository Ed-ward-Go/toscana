define(
    [
        'jquery',
        'Magento_Ui/js/model/messageList',
        'mage/translate'
    ],
    function ($,messageList,$t) {
        'use strict';
        return {
            /**
             * Validate something
             *
             * @returns {boolean}
             */
            validate: function() {
                var shipping = $('.checkout-shipping-method input[class="radio"]:checked').val() || '';
                if($('.js-click.active').length <= 0 && shipping == 'pickup_pickup'){
                    var message = $t('Por favor seleccione una de nuestras oficinas');
                    $('#list-offices').addClass('blink_me')
                    setTimeout(function(){ $('.opc-progress-bar  .opc-progress-bar-item:first > span').trigger('click');  }, 2000);
                    setTimeout(function(){ $('#list-offices').removeClass('blink_me');  }, 5500);


                    messageList.addErrorMessage({ message: message});
                    $('#list-offices').slideDown('slow');
                    return false;

                }
                return true;
            }
        }
    }
);
