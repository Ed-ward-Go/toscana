define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'pointofsalepayment',
                component: 'Aventi_PickUpWithOffices/js/view/payment/method-renderer/pointofsalepayment-method'
            },
            {
                type: 'checkmo',
                component: 'Magento_OfflinePayments/js/view/payment/method-renderer/checkmo-method'
            },
            {
                type: 'banktransfer',
                component: 'Magento_OfflinePayments/js/view/payment/method-renderer/banktransfer-method'
            },
            {
                type: 'cashondelivery',
                component: 'Magento_OfflinePayments/js/view/payment/method-renderer/cashondelivery-method'
            },
            {
                type: 'purchaseorder',
                component: 'Magento_OfflinePayments/js/view/payment/method-renderer/purchaseorder-method'
            }
        );
        return Component.extend({});
    }
);