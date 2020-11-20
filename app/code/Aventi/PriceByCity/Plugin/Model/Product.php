<?php

namespace Aventi\PriceByCity\Plugin\Model;

class Product
{
    private $logger;

    private $helper;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\PriceByCity\Helper\Data $helper
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $result = $this->helper->calculatePriceBySource($subject->getId());
        return $result;
    }
}
