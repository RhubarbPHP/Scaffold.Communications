<?php

namespace Rhubarb\Scaffolds\Communications\BackgroundTasks;

use Rhubarb\Scaffolds\BackgroundTasks\BackgroundTask;
use Rhubarb\Scaffolds\BackgroundTasks\Models\BackgroundTaskStatus;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationEmail;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;

class CommunicationBackgroundTask extends BackgroundTask
{

    /**
     * Executes the long running code.
     *
     * @return void
     */
    public function execute(BackgroundTaskStatus $status)
    {
        $status->Message = "Processing Communications to be sent";
        $status->save();

        $communicationID = $status->TaskSettings["CommunicationID"];

        $communication = new Communication($communicationID);
        CommunicationProcessor::sendCommunication($communication);
    }
}
