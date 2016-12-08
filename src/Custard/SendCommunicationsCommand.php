<?php

namespace Rhubarb\Scaffolds\Communications\Custard;

use Rhubarb\Custard\Command\CustardCommand;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;
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
            ->addArgument('communicationID', InputArgument::OPTIONAL, 'The id of the communication you wish to send');
    }

    protected function executeWithConnection(InputInterface $input, OutputInterface $output)
    {
        parent::executeWithConnection($input, $output);

        $communicationID = $input->getArgument('communicationID');
        $limit = $input->getOption("limit");

        if (isset($communicationID)) {
            $communication = new Communication($communicationID);
            CommunicationProcessor::sendCommunication($communication);
        } else {
            $unsentCommunicationsArray = Communication::findUnsentCommunications();

            if ($limit){
                $unsentCommunicationsArray->setRange(0, $limit);
            }

            foreach ($unsentCommunicationsArray as $communication) {
                CommunicationProcessor::sendCommunication($communication);
            }
        }
    }
}
