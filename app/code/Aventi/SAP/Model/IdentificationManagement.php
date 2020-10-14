<?php


namespace Aventi\SAP\Model;

/**
 * Class IdentificationManagement
 *
 * @package Aventi\SAP\Model
 */
class IdentificationManagement implements \Aventi\SAP\Api\IdentificationManagementInterface
{


    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    public function __construct(\Magento\Checkout\Model\Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function putIdentification($param)
    {

        $param = substr(preg_replace('/[^0-9]/','',$param),0,20);

        $quote = $this->session->getQuote();
        $billAddress  =  $quote->getBillingAddress();
        $billAddress->setData('identification_customer',$param);
        $billAddress->save();

        $shippingAddress  =  $quote->getShippingAddress();
        $shippingAddress->setData('identification_customer',$param);
        $shippingAddress->save();

    }
}