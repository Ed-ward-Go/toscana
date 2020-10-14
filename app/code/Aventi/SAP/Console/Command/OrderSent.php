<?php


namespace Aventi\SAP\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Price
 *
 * @package Aventi\SAP\Console\Command
 */
class OrderSent extends Command
{

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;
    /**
     * @var \Aventi\SAP\Model\Sync\SendToSAP
     */
    private $sendToSAP;

    public function __construct(\Aventi\SAP\Model\Sync\SendToSAP $sendToSAP,
                                \Magento\Framework\App\State $state)
    {
        parent::__construct();

        $this->state = $state;
        $this->sendToSAP = $sendToSAP;
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        $this->state->setAreaCode( \Magento\Framework\App\Area::AREA_CRONTAB);
        $this->sendToSAP->setOutput($output);
        $this->sendToSAP->orderSent();

    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:sent");
        $this->setDescription("Sync order sent to  customer");
        parent::configure();
    }
}

