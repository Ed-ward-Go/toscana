<?php


namespace Aventi\SAP\Cron;

/**
 * Class DraftStatus
 *
 * @package Aventi\SAP\Cron
 */
class DraftStatus
{
        
    /**     
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
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
        $this->logger->addInfo("Cronjob DraftStatus is executed.");
        $this->sendToSAP->draftStatus();
    }
}

