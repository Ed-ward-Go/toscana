<?php


namespace Aventi\SAP\Plugin\Magento\Checkout\Block\Checkout;

/**
 * Class LayoutProcessor
 *
 * @package Aventi\SAP\Plugin\Magento\Checkout\Block\Checkout
 */
class LayoutProcessor
{

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result,
        $jsLayout
    ) {

        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['identification_customer'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'options' => [],
                'id' => 'identification_customer'
            ],
            'dataScope' =>  'shippingAddress.identification_customer',
            'label' => __('Identification customer'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [
                'required-entry' => true,
                'validate-number' => true,
                'max_text_length' => 12
            ],
            'sortOrder' => 50,
            'id' => 'identification_customer'
        ];


        //Your plugin code
        return $result;
    }
}

