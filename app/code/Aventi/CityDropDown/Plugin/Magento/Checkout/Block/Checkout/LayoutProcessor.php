<?php


namespace Aventi\CityDropDown\Plugin\Magento\Checkout\Block\Checkout;

class LayoutProcessor
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result,
        $jsLayout
    ) {


        foreach ($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                 ['payment']['children']['payments-list']['children'] as &$child)
        {



            if (isset($child['children']['form-fields'])) {

                $files = [
                     'country_id' => 100,
                     'region_id' => 220,
                      'city' => 210,
                      'postcode' => 230,
                     'street' =>  240
                ];

                foreach ($files as $key=>$f){
                    $child['children']['form-fields']['children'][$key]['sortOrder'] = $f;

                    if($key == 'city'){
                       /* $child['children']['form-fields']['children'][$key]['component'] =  'Magento_Ui/js/form/element/select';
                        $child['children']['form-fields']['children'][$key]['config']['elementTmpl'] = 'ui/form/element/select';
                        $child['children']['form-fields']['children'][$key]['options'] = [
                            [
                                'value' => '',
                                'label' => 'Choose a city',
                            ],
                            [
                                'value' => 'citycode',
                                'label' => 'cityname',
                            ]
                        ];
                        $this->logger->error(print_r($child['children']['form-fields']['children'][$key]['config'],true));*/
                    }

                    
                }
            }
        }


       //


        return $result;

    }
}
