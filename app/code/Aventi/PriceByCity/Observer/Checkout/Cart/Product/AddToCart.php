<?php

namespace Aventi\PriceByCity\Observer\Checkout\Cart\Product;

use Magento\Framework\Event\ObserverInterface;

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
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    public function __construct(
        \Aventi\PriceByCity\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /*$item = $observer->getEvent()->getData('quote_item');
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        $price = $this->helper->calculatePriceBySource($item->getProductId());
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);*/
    }
}
