<?php

namespace Aventi\SAP\Plugin\Magento\Checkout\Model;

/**
 * Class LayoutProcessor
 *
 * @package Aventi\SAP\Plugin\Magento\Checkout\Block\Checkout
 */
class ShippingInformationManagement
{

    private $addressExtensionFactory;

    private $logger;

    private $addressRepositoryInterface;

    private $quoteRepository;

    public function __construct(
        \Magento\Checkout\Api\Data\ShippingInformationExtensionFactory $addressExtensionFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepositoryInterface,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    )
    {
        $this->addressExtensionFactory = $addressExtensionFactory;
        $this->logger = $logger;
        $this->addressRepositoryInterface = $addressRepositoryInterface;
        $this->quoteRepository = $quoteRepository;
    }

    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $customerAddressId = $addressInformation->getShippingAddress()->getCustomerAddressId();
        if(!empty($customerAddressId)){
            $customerAddressRepository = $this->addressRepositoryInterface->getById($customerAddressId);

            $quote = $this->quoteRepository->getActive($cartId);            
            
            try {
                $serie = $customerAddressRepository->getCustomAttribute('serie');
                $warehouseGroup = $customerAddressRepository->getCustomAttribute('warehouse_group');
                if($serie && $warehouseGroup){
                    $quote->getShippingAddress()->setData('serie', $serie->getValue());
                    $quote->getShippingAddress()->setData('warehouse_group', $warehouseGroup->getValue());
                }
            } catch (\Exception $e) {
                $this->logger->error("Error: ".$e->getMessage());
            }

            /*$customerAddressExtensionAttributes = $customerAddressRepository->getExtensionAttributes();

            $quoteAddressExtensionAttribute = $this->addressExtensionFactory->create();

            $quoteAddressExtensionAttribute->setSerie($customerAddressExtensionAttributes->getSerie());
            $quoteAddressExtensionAttribute->setWarehouseGroup($customerAddressExtensionAttributes->getWarehouseGroup());
            $this->logger->error(json_encode($customerAddressExtensionAttributes->getWarehouseGroup()));   
            $addressInformation->setExtensionAttributes($quoteAddressExtensionAttribute);*/
        }        

        //$this->logger->error("Entro panita". json_encode($addressInformation->getExtensionAttributes()->getSerie()));   
        
        
    }
}




?>