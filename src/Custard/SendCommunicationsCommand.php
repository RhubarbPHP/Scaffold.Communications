<?php

namespace Rhubarb\Scaffolds\Communications\Custard;

use Rhubarb\Custard\Command\CustardCommand;
use Rhubarb\Scaffolds\Communications\BackgroundTasks\CommunicationBackgroundTask;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommunicationsCommand extends CustardCommand
{
    protected function configure()
    {
        $this->setName('communication:send-unsent')
            ->setDescription('Sends unsent Communications to the their relevant recipients')
            ->addArgument('communicationID', InputArgument::OPTIONAL, 'The id of the communication you wish to send');;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $communicationID = $input->getArgument('communicationID');

        CommunicationBackgroundTask::initiate([$communicationID]);
    }
}
