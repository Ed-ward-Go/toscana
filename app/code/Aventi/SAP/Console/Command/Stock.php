<?php

namespace Aventi\SAP\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Stock
 *
 * @package Aventi\SAP\Console\Command
 */
class Stock extends Command
{
    const PROCESS_SELECTED = 'Process';

    const PROCESS_LIST = [
        0 => 'stock',
        1 => 'fastStock'
    ];
    /**
     * @var \Aventi\SAP\Model\Sync\Product
     */
    private $productManager;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        \Aventi\SAP\Model\Sync\Product $product,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct();
        $this->productManager = $product;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $process = (int)$input->getArgument(self::PROCESS_SELECTED);
        if ($process < 0 || $process > 2) {
            $output->writeln("<error>Process no found :(</error>");
            exit;
        }
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        $this->productManager->setOutput($output);
        $this->productManager->updateStock($process);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("aventi:sap:stock");
        $this->setDescription("Sync stock SAP to Magento 0 => Stock, 1 => Fast Stock");
        $this->setDefinition([
            new InputArgument(self::PROCESS_SELECTED, null, "Process"),
        ]);
        parent::configure();
    }
}
