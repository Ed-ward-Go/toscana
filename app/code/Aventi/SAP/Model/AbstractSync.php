<?php

namespace Aventi\SAP\Model;

use Bcn\Component\Json\Reader;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class AbstractSync
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output = null;

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    private function getOutput()
    {
        return $this->output;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * Instance the class reader for json
     *
     * @param $filePath
     * @return Reader
     * @date 14/11/18
     */
    protected function getJsonReader($filePath)
    {
        if (file_exists($filePath)) {
            $this->file = fopen($filePath, "r");
            return new Reader($this->file);
        }
    }

    /**
     * Close the file
     *
     * @date 15/11/18
     */
    private function closeFile()
    {
        @fclose($this->file);
    }

    protected function startProgressBar($total)
    {
        $output = $this->getOutput();
        if ($output) {
            $progressBar = new ProgressBar($output, $total);
            $progressBar->start();
            return $progressBar;
        }
        return false;
    }

    protected function advanceProgressBar($progressBar)
    {
        $output = $this->getOutput();
        if ($output) {
            $progressBar->advance();
        }
    }

    protected function finishProgressBar($progressBar, $start, $rows)
    {
        $output = $this->getOutput();
        if ($output) {
            $progressBar->finish();
            $output->writeln(sprintf("\nInteraction %s", ($start / $rows)));
        }
        $this->closeFile();
    }
}
