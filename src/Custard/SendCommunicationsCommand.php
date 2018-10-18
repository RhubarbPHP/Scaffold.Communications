<?php

namespace Rhubarb\Scaffolds\Communications\Custard;

use Rhubarb\Custard\Command\CustardCommand;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;
use Rhubarb\Scaffolds\Communications\Settings\CommunicationProcessorSettings;
use Rhubarb\Stem\Custard\RequiresRepositoryCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommunicationsCommand extends RequiresRepositoryCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('communication:send-unsent')
            ->setDescription('Sends unsent Communications to the their relevant recipients')
            ->addOption("limit", "l", InputOption::VALUE_OPTIONAL, "Limits sending to n number of entries", 0)
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'If specified, no emails will be sent, but the numbers of emails that would be sent will be listed')
            ->addArgument('communicationID', InputArgument::OPTIONAL, 'The id of the communication you wish to send');
    }

    protected function executeWithConnection(InputInterface $input, OutputInterface $output)
    {
        parent::executeWithConnection($input, $output);

        $communicationID = $input->getArgument('communicationID');
        $limit = $input->getOption("limit");

        if (isset($communicationID)) {
            $communication = new Communication($communicationID);

            if (!$this->input->getOption('dry-run')) {
                CommunicationProcessor::sendCommunication($communication);
            }
        } else {
            $unsentCommunicationsArray = Communication::findUnsentCommunications();

            if ($limit){
                $unsentCommunicationsArray->setRange(0, $limit);
            }


            $maxPerSecond = CommunicationProcessorSettings::singleton();
            $timePerEmail = 1 / $maxPerSecond;

            if ($this->input->getOption('dry-run')) {
                $output->writeln("Dry run: would send " . count($unsentCommunicationsArray) . " communications selected for sending.");
            }

            foreach ($unsentCommunicationsArray as $communication) {
                $startTime = microtime(true) * 1000000;

                if (!$this->input->getOption('dry-run')) {
                    CommunicationProcessor::sendCommunication($communication);
                } else {
                    $output->writeln("Dry run: sending ".$communication->CommunicationID);
                }

                $endTime = microtime(true) * 1000000;
                $diff = $endTime - $startTime;
                $timeToSleep = $timePerEmail - $diff;

                if ($timeToSleep > 0) {
                    usleep($timeToSleep * 1000000);
                }
            }
        }
    }
}
