<?php


namespace Aventi\SAP\Cron;

/**
 * Class OrderSent
 *
 * @package Aventi\SAP\Cron
 */
class OrderSent
{

    protected $logger;
    /**
     * @var \Aventi\SAP\Model\Sync\Product
     */
    private $product;
    /**
     * @var \Aventi\SAP\Model\Sync\SendToSAP
     */
    private $sendToSAP;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger,
                                \Aventi\SAP\Model\Sync\SendToSAP $sendToSAP
    )
    {
        $this->logger = $logger;

        $this->sendToSAP = $sendToSAP;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob OrderSent is executed.");
        $this->sendToSAP->orderSent();
    }
}

