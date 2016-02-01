<?php

namespace Rhubarb\Scaffolds\Communications\BackgroundTasks;

use Rhubarb\Scaffolds\BackgroundTasks\BackgroundTask;
use Rhubarb\Scaffolds\BackgroundTasks\Models\BackgroundTaskStatus;
use Rhubarb\Scaffolds\Communications\Models\Communication;

class CommunicationProcess extends BackgroundTask
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

        $unsentCommunications = Communication::FindUnsentCommunications();

        foreach($unsentCommunications as $communication) {

        }
    }
}
