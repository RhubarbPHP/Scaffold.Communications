<?php

namespace Rhubarb\Scaffolds\Communications\BackgroundTasks;

use Rhubarb\Crown\Exceptions\ImplementationException;
use Rhubarb\Scaffolds\BackgroundTasks\BackgroundTask;
use Rhubarb\Scaffolds\BackgroundTasks\Models\BackgroundTaskStatus;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;

class CommunicationBackgroundTask extends BackgroundTask
{
    public function execute(BackgroundTaskStatus $status)
    {
        $status->Message = "Processing Communications to be sent";
        $status->save();

        $communicationID = $status->TaskSettings["CommunicationID"];

        if (!$communicationID) {
            throw new ImplementationException("Communication Background Task must have a Communication ID");
        }

        $communication = new Communication($communicationID);
        CommunicationProcessor::sendCommunication($communication);
    }
}
