<?php

namespace Rhubarb\Scaffolds\Communications\EmailProviders;

use Rhubarb\Crown\Email\Email;
use Rhubarb\Crown\Email\EmailProvider;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Scaffolds\Communications\BackgroundTasks\CommunicationBackgroundTask;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationEmail;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;

class CommunicationEmailProvider extends EmailProvider
{
    public function sendEmail(Email $email)
    {
        $communication = Communication::fromEmail($email);
        if (CommunicationProcessor::getEmailProvider() instanceof UnitTestingEmailProvider) {
            $communication->Completed = true;
            $communication->save();
            foreach ($communication->Emails as $communicationEmail) {
                $communicationEmail->Sent = true;
                $communicationEmail->save();
            }
        } else {
            CommunicationBackgroundTask::initiate(["CommunicationID" => $communication->CommunicationID]);
        }
    }
}
