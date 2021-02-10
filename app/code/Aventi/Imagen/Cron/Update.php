<?php declare(strict_types=1);


namespace Aventi\Imagen\Cron;


class Update
{

    protected $logger;
    /**
     * @var \Aventi\Imagen\Model\Process
     */
    private $process;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aventi\Imagen\Model\Process $process
)
    {
        $this->logger = $logger;
        $this->process = $process;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob Update is executed.");
        $this->process->update();
    }
}

