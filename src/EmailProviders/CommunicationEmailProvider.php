<?php

namespace Rhubarb\Scaffolds\Communications\EmailProviders;

use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Crown\Sendables\Email\EmailProvider;
use Rhubarb\Crown\Sendables\Sendable;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Scaffolds\Communications\BackgroundTasks\CommunicationBackgroundTask;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;

class CommunicationEmailProvider extends EmailProvider
{
    public function send(Sendable $email)
    {
        $communication = Communication::fromEmail($email);
        if (CommunicationProcessor::getEmailProvider() instanceof UnitTestingEmailProvider) {
            $communication->Completed = true;
            $communication->save();
            foreach ($communication->Items as $communicationEmail) {
                $communicationEmail->Sent = true;
                $communicationEmail->save();
            }
        } else {
            CommunicationBackgroundTask::initiate(["CommunicationID" => $communication->CommunicationID]);
        }
    }
}
