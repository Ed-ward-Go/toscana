<?php


namespace Aventi\SAP\Cron;

/**
 * Class ProductFaster
 *
 * @package Aventi\SAP\Cron
 */
class ProductFaster
{

    protected $logger;
    /**
     * @var \Aventi\SAP\Model\Sync\Product
     */
    private $product;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger,
                                \Aventi\SAP\Model\Sync\Product $product
    )
    {
        $this->logger = $logger;
        $this->product = $product;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob Product Faster is executed.");
        $this->product->updateProduct(1);
    }
}

