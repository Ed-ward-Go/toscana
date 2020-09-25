<?php
/**
 * Add file comment to orders
 * Copyright (C) 2018  
 * 
 * This file is part of Aventi/OrderComment.
 * 
 * Aventi/OrderComment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aventi\SAP\Observer\Sales;



class ModelServiceQuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{

    private $logger;

    private $summaryRepository;

    private $companyUser;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aheadworks\CreditLimit\Api\SummaryRepositoryInterface $summaryRepository,
        \Aheadworks\Ca\Model\Service\CompanyUserService $companyUser
    )
    {
        $this->logger = $logger;
        $this->summaryRepository = $summaryRepository;
        $this->companyUser = $companyUser;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        $order = $observer->getEvent()->getOrder();
        /** @var $order \Magento\Sales\Model\Order **/

        $quote = $observer->getEvent()->getQuote();
        /** @var $quote \Magento\Quote\Model\Quote **/
            
        if($quote->getPayment()->getMethod() == "aw_credit_limit"){

            $customerRoot = $this->companyUser->getRootUserForCustomer($quote->getCustomer()->getId());
            if($customerRoot->getId()){
                
                $summary = $this->summaryRepository->getByCustomerId($customerRoot->getId());
                $grandTotal = $quote->getGrandTotal();
                $operation = $summary->getCreditAvailable() - $grandTotal;
                
                if($operation < 0){
                    $order->setData('credit_exceeded', 1);
                    $order->save();
                }
            }                                    
        }
        /**
         * Save the customer identification
         */
        $identificationCustomer =  $quote->getShippingAddress()->getData('identification_customer');

        $serie =  $quote->getShippingAddress()->getData('serie');
        $warehouseGroup =  $quote->getShippingAddress()->getData('warehouse_group');

        $order->getShippingAddress()->setData('identification_customer',$identificationCustomer);
        $order->getBillingAddress()->setData('identification_customer',$identificationCustomer);

        $order->getShippingAddress()->setData('serie',$serie);
        $order->getBillingAddress()->setData('serie',$serie);

        $order->getShippingAddress()->setData('warehouse_group',$warehouseGroup);
        $order->getBillingAddress()->setData('warehouse_group',$warehouseGroup);



    }
}
