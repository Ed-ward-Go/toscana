<?php

namespace Aventi\SAP\Plugin\Aheadworks\Ca\Controller\User;

/**
 * @class Aventi\SAP\Plugin\Aheadworks\Ca\Controller\User\SavePost
 */
class SavePost {

    /**     
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    
    /**     
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    
    /**     
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    private $dataAddressFactory;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var \Aventi\SAP\Helper\SAP
     */
    private $helperSAP;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $dataAddressFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Aventi\SAP\Helper\SAP $helperSAP        
    ){
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->dataAddressFactory = $dataAddressFactory;
        $this->addressRepository = $addressRepository;
        $this->helperSAP = $helperSAP;
    }

    public function afterExecute(
        \Aheadworks\Ca\Controller\User\SavePost $subject,
        $result        
    ){                

        if ($this->customerSession->isLoggedIn()) {
            
            $customerCreated = $this->customerSession->getCustomerCreated();
            $customerCreatedId = $this->customerRepository->get($customerCreated);
            $customerId = $this->customerSession->getCustomer()->getId();
            $customer = $this->customerRepository->getById($customerId);            
            $customerAddresses = $customer->getAddresses();
            
            foreach ($customerAddresses as $address) {
                $customerAddress = $this->dataAddressFactory->create();
                $customerAddress->setCustomerId($customerCreatedId->getId())
                ->setFirstname($customer->getFirstname())
                ->setLastname(' '. $customer->getLastName())
                ->setCountryId('EC')
                ->setRegion($address->getRegion())
                ->setRegionId($address->getRegionId())
                ->setPostcode($address->getPostCode())
                ->setCity($address->getCity())
                ->setTelephone($address->getTelephone())
                ->setIsDefaultShipping(1)
                ->setIsDefaultBilling(1)
                ->setStreet(['0' => $address->getStreet()[0]]);
                $this->addressRepository->save($customerAddress);
                $addressSAP = $this->helperSAP->getAddressSAP($address->getId());
                
                $this->helperSAP->managerCustomerAddressSAPForce($addressSAP, (int)$customerAddress->getId());
            } 
            $this->customerSession->unsCustomerCreated();           
        }        
        return $result;
    }

}


?>