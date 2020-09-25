<?php


namespace Aventi\SAP\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class TestEmail extends Command
{

    const PROCESS_SELECTED = "Process";

    const PROCESS_LIST = [
        0 => 'email_cancel',
        1 => 'order_sent'       
    ];

    private $state;
    private $sendToSAP;
    private $logger;    

    /**
     * SycnOrder constructor.
     *
     * @param \Magento\Framework\App\State $state
     * @param \Aventi\SAP\Model\sendToSAP $sendToSAP
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Aventi\Rocket\Helper\Data $rocket
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Aventi\SAP\Model\Sync\SendToSAP $sendToSAP,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->state = $state;
        $this->sendToSAP = $sendToSAP;
        $this->logger = $logger;
        parent::__construct();
    }
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @author  Carlos Hernan Aguilar <caguilar@aventi.co>
     * @date 21/03/19
     * @return int|null|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        $this->state->setAreaCode( \Magento\Framework\App\Area::AREA_FRONTEND);
        $process = (int)$input->getArgument(self::PROCESS_SELECTED);

        if( $process < 0 || $process > 3 ){
            $output->writeln("<error>Process no found :(</error>");
            exit;
        }

        $this->sendToSAP->setOutput($output);
        $this->sendToSAP->testSendMail($process);        
        
        $output->writeln("<info> finished :) number ". $process.'</info>');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:test:email");
        $this->setDescription("Test custom emails [0 => email_cancel, 1 => order_sent]");        
        $this->setDefinition([
            new InputArgument(self::PROCESS_SELECTED, null, "Process"),
        ]);
        parent::configure();
    }



}
