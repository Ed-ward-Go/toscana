<?php

namespace Aventi\PickUpWithOffices\Observer\Payment;

use Magento\Framework\Event\ObserverInterface;

class PaymentMethodAvailable implements ObserverInterface
{

    private $_checkoutSession;
    

    private $logger;

    public function __construct(
        \Magento\Checkout\Model\Session $_checkoutSession,         
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_checkoutSession = $_checkoutSession;        
        $this->logger = $logger;                
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        $methodTitle = $this->_checkoutSession->getQuote()->getShippingAddress()->getShippingMethod();  
        
        $paymentCode = $observer->getEvent()->getMethodInstance()->getCode();
        if($methodTitle != "pickup_pickup" && $paymentCode == "pointofsalepayment"){

            $checkResult = $observer->getEvent()->getResult();
            $checkResult->setData('is_available', false); 

        }
    }
}