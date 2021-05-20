<?php


namespace Aventi\SAP\Cron;

/**
 * Class CustomerFast
 *
 * @package Aventi\SAP\Cron
 */
class Customer
{

    protected $logger;
    /**
     * @var \Aventi\SAP\Model\Sync\Customer
     */
    private $customer;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger,
                                \Aventi\SAP\Model\Sync\Customer $customer
    )
    {
        $this->logger = $logger;
        $this->customer = $customer;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("------------------Cronjob Customer is executed-------------------");
        $this->customer->customer(0);
    }
}

