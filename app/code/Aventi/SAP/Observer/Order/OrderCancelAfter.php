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

namespace Aventi\SAP\Observer\Order;



use Magento\Framework\Exception\MailException;

class OrderCancelAfter implements \Magento\Framework\Event\ObserverInterface
{

    private $logger;

    private $dataEmail;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\SAP\Helper\DataEmail $dataEmail,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->logger = $logger;
        $this->dataEmail = $dataEmail;
        $this->orderRepository = $orderRepository;
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

        if($order){
            try {
                $this->dataEmail->sendOrderCancelEmail(
                    $order->getCustomerEmail(),
                    $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                    $order->getIncrementId(),
                    $order->getPayment()->getMethodInstance()->getTitle(),
                    $order
                );
                $order->setData('sap_notification_send', 1);
                $this->orderRepository->save($order);
            } catch (MailException $exception) {
                $this->logger->error("The cancel order don't send email: " . $exception->getMessage());
            }
        }

    }
}
