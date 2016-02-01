<?php

namespace Rhubarb\Scaffolds\Communications\BackgroundTasks;

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

        if (isset($communicationID)) {
            $communication = new Communication($communicationID);
            CommunicationProcessor::sendCommunication($communication);
        } else {
            $unsentCommunicationsArray = Communication::FindUnsentCommunications();
            foreach($unsentCommunicationsArray as $communication) {
                CommunicationProcessor::sendCommunication($communication);
            }
        }
    }
}
