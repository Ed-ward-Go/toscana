<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aventi\PriceByCity\Block\Checkout;

class AttributeMerger extends \Magento\Checkout\Block\Checkout\AttributeMerger
{
    private $addressHelper;

    private $customerSession;

    private $customerRepository;

    private $directoryHelper;

    private $data;

    private $logger;

    public function __construct(
        \Magento\Customer\Helper\Address $addressHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Aventi\LocationPopup\Helper\Data $data,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct(
            $addressHelper,
            $customerSession,
            $customerRepository,
            $directoryHelper
        );

        $this->directoryHelper = $directoryHelper;
        $this->data = $data;
        $this->logger = $logger;
    }

    /**
     * Retrieve UI field configuration for given attribute
     *
     * @param string $attributeCode
     * @param array $attributeConfig
     * @param array $additionalConfig field configuration provided via layout XML
     * @param string $providerName name of the storage container used by UI component
     * @param string $dataScopePrefix
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function getFieldConfig(
        $attributeCode,
        array $attributeConfig,
        array $additionalConfig,
        $providerName,
        $dataScopePrefix
    ) {
        // street attribute is unique in terms of configuration, so it has its own configuration builder
        if (isset($attributeConfig['validation']['input_validation'])) {
            $validationRule = $attributeConfig['validation']['input_validation'];
            $attributeConfig['validation'][$this->inputValidationMap[$validationRule]] = true;
            unset($attributeConfig['validation']['input_validation']);
        }

        if ($attributeConfig['formElement'] == 'multiline') {
            return $this->getMultilineFieldConfig($attributeCode, $attributeConfig, $providerName, $dataScopePrefix);
        }

        $uiComponent = isset($this->formElementMap[$attributeConfig['formElement']])
            ? $this->formElementMap[$attributeConfig['formElement']]
            : 'Magento_Ui/js/form/element/abstract';
        $elementTemplate = isset($this->templateMap[$attributeConfig['formElement']])
            ? 'ui/form/element/' . $this->templateMap[$attributeConfig['formElement']]
            : 'ui/form/element/' . $attributeConfig['formElement'];

        $element = [
            'component' => isset($additionalConfig['component']) ? $additionalConfig['component'] : $uiComponent,
            'config' => [
                // customScope is used to group elements within a single form (e.g. they can be validated separately)
                'customScope' => $dataScopePrefix,
                'customEntry' => isset($additionalConfig['config']['customEntry'])
                    ? $additionalConfig['config']['customEntry']
                    : null,
                'template' => 'ui/form/field',
                'elementTmpl' => isset($additionalConfig['config']['elementTmpl'])
                    ? $additionalConfig['config']['elementTmpl']
                    : $elementTemplate,
                'tooltip' => isset($additionalConfig['config']['tooltip'])
                    ? $additionalConfig['config']['tooltip']
                    : null
            ],
            'dataScope' => $dataScopePrefix . '.' . $attributeCode,
            'label' => $attributeConfig['label'],
            'provider' => $providerName,
            'sortOrder' => isset($additionalConfig['sortOrder'])
                ? $additionalConfig['sortOrder']
                : $attributeConfig['sortOrder'],
            'validation' => $this->mergeConfigurationNode('validation', $additionalConfig, $attributeConfig),
            'options' => $this->getFieldOptions($attributeCode, $attributeConfig),
            'filterBy' => isset($additionalConfig['filterBy']) ? $additionalConfig['filterBy'] : null,
            'customEntry' => isset($additionalConfig['customEntry']) ? $additionalConfig['customEntry'] : null,
            'visible' => isset($additionalConfig['visible']) ? $additionalConfig['visible'] : true,
        ];

        if (isset($additionalConfig['disabled']) && $additionalConfig['disabled'] != null) {
            $element['disabled'] = "disabled";
        }

        if (isset($additionalConfig['value']) && $additionalConfig['value'] != null) {
            $element['value'] = "";
        }

        if (isset($attributeConfig['value']) && $attributeConfig['value'] != null) {
            $element['value'] = $attributeConfig['value'];
        } elseif (isset($attributeConfig['default']) && $attributeConfig['default'] != null) {
            $element['value'] = $attributeConfig['default'];
        } else {
            $defaultValue = $this->getDefaultValue($attributeCode);
            if (null !== $defaultValue) {
                $element['value'] = $defaultValue;
            }
        }

        return $element;
    }

    /**
     * Returns default attribute value.
     *
     * @param string $attributeCode
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return null|string
     */
    protected function getDefaultValue($attributeCode) : ?string
    {
        if ($attributeCode === 'country_id') {
            return $this->directoryHelper->getDefaultCountry();
        }

        $customer = $this->getCustomer();

        $attributeValue = null;
        switch ($attributeCode) {
            case 'prefix':
                if ($customer != null) {
                    $attributeValue = $customer->getPrefix();
                }
                break;
            case 'firstname':
                if ($customer != null) {
                    $attributeValue = $customer->getFirstname();
                }
                break;
            case 'middlename':
                if ($customer != null) {
                    $attributeValue = $customer->getMiddlename();
                }
                break;
            case 'lastname':
                if ($customer != null) {
                    $attributeValue = $customer->getLastname();
                }
                break;
            case 'suffix':
                if ($customer != null) {
                    $attributeValue = $customer->getSuffix();
                }
                break;
            /*case 'postcode':
                $attributeValue = (int)$this->data->getValue()['postcode'];
                break;
            case 'city':
                $attributeValue = $this->data->getValue()['city'];
                break;
            case 'region_id':
                $attributeValue = $this->data->getValue()['region'];
                break;
            case 'region':
                $attributeValue = $this->data->getRegionName($this->data->getValue()['region']);
                break;*/
        }
        return $attributeValue;
    }
}
