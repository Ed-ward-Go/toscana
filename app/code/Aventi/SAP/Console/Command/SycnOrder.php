<?php


namespace Aventi\SAP\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class SycnOrder extends Command
{

    const PROCESS_SELECTED = "Process";

    const PROCESS_LIST = [
        0 => 'cancelPendingOrders',
        1 => 'completedOrdersToSap',
        2 => 'pendingOrdersToSap',
        3 => 'errorOrdersToSap'
    ];

    private $state;
    private $sendToSAP;
    private $logger;
    private $rocket;

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

        try {
            switch ($process) {
                case 0 :
                     $response = $this->sendToSAP->cancelPendingOrders();
                    break;
                case 1:
                case 2:
                    $response = $this->sendToSAP->completedOrderToSAP();
                    break;
                case 3:
                    $response = $this->sendToSAP->errorOrderToSAP();
                    break;
                default:
                    break;
            }
        }catch (\Exception $e){
            $this->logger->error($e->getMessage());
            $output->writeln("<error>{$e->getMessage()}(</error>");
        }
        if(is_array($response) && !empty($response)) {
            $table = new Table($output);
            $table
                ->setHeaders($response['title'])
                ->setRows([$response['body']]);
            $table->render();
        }
        $output->writeln("<info> finished :) number ". $process.'</info>');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sycn:order");
        $this->setDescription("Synchronize purchase orders with SAP");
        $this ->setHelp('This command allows you to create a  order in SAP [0:cancelPendingOrders,1:completedOrdersToSap,2:pendingOrdersToSap ]');
        $this->setDefinition([
            new InputArgument(self::PROCESS_SELECTED, null, "Process"),
        ]);
        parent::configure();
    }



}
