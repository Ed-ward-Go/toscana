<?php


namespace Aventi\SAP\Cron;

/**
 * Class PriceFaster
 *
 * @package Aventi\SAP\Cron
 */
class PriceFaster
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
        $this->logger->addInfo("Cronjob price faster is executed.");
        $this->product->updatePrice(2, 1);
    }
}

