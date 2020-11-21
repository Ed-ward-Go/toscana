<?php

namespace Aventi\PriceByCity\Observer\Checkout\Cart\Product;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class AddToCart implements ObserverInterface
{

    /**
     * @var Aventi\PriceByCity\Helper\Data
     */
    private $helper;

    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Aventi\PriceByCity\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger)
    {
        $this->helper = $helper;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $item = $observer->getEvent()->getData('quote_item');
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        $price = $this->helper->calculatePriceBySource($item->getProductId());
        $this->logger->info($price);
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);

    }

}
