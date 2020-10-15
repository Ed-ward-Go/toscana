<?php


namespace Aventi\SAP\Cron;

/**
 * Class Price
 *
 * @package Aventi\SAP\Cron
 */
class Price
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
        $this->logger->addInfo("Cronjob price is executed.");
        $this->product->updatePrice(1);
    }
}

