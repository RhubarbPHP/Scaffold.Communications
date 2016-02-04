<?php

namespace Rhubarb\Scaffolds\Communications\EmailProviders;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Email\Email;
use Rhubarb\Crown\Email\EmailProvider;
use Rhubarb\Crown\Request\CliRequest;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Scaffolds\Communications\BackgroundTasks\CommunicationBackgroundTask;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;

class CommunicationEmailProvider extends EmailProvider
{
    public function sendEmail(Email $email)
    {
        $communication = Communication::fromEmail($email);

        //  Check to ensure emails are not sent when using UnitTestingEmailProvider
        if (CommunicationProcessor::getEmailProvider() instanceof UnitTestingEmailProvider) {
            $communication->Completed = true;
            $communication->save();
            foreach ($communication->Emails as $communicationEmail) {
                $communicationEmail->Sent = true;
                $communicationEmail->save();
            }
        } else {
            //  If we are already running on the Command line therefore we do not need to spawn another background
            //  task
            if (Context::currentRequest() instanceof CliRequest) {
                CommunicationProcessor::sendCommunication($communication);
            } else {
                CommunicationBackgroundTask::initiate(["CommunicationID" => $communication->CommunicationID]);
            }
        }
    }
}
