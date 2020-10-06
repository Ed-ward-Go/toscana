<?php
namespace Aventi\Credit\Observer\Sales;

use Aventi\Credit\Model\Checkout\ConfigProvider;
use Aventi\Credit\Model\Service\CreditService;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class OrderSubmitAfter implements ObserverInterface
{
    /**
     * @var CreditService
     */
    private $creditService;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CreditService $creditService,
        LoggerInterface $logger
    ) {
        $this->creditService = $creditService;
        $this->logger = $logger;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment();
        $customerId = $order->getCustomerId();
        if ($payment->getMethod() == ConfigProvider::METHOD_CODE) {
            $this->creditService->managementCreateOrder($customerId, $order->getGrandTotal());
        }
    }
}
