<?php

namespace Aventi\PriceByCity\Observer\Checkout\Cart\Product;

use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\Data\CartItemInterface;

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
     * @var \Aventi\LocationPopup\Helper\Data
     */
    private $locationHelper;
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    public function __construct(
        \Aventi\PriceByCity\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Aventi\LocationPopup\Helper\Data $locationHelper,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->locationHelper = $locationHelper;
        $this->quoteRepository = $quoteRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $source = $this->locationHelper->getValue();

        $item = $observer->getEvent()->getData('quote_item');
        $quote = $this->quoteRepository->get($item->getQuoteId());
        $quote->setData('source_code', $source['id']);
        $this->quoteRepository->save($quote);
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        $price = $this->helper->calculatePriceBySource($item->getProductId());
        $this->logger->info($price);
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);
    }
}
