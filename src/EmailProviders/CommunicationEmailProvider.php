<?php

namespace Rhubarb\Scaffolds\Communications\EmailProviders;

use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Crown\Sendables\Email\EmailProvider;
use Rhubarb\Crown\Sendables\Sendable;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Scaffolds\Communications\BackgroundTasks\CommunicationBackgroundTask;
use Rhubarb\Scaffolds\Communications\CommunicationPackages\CommunicationPackage;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;

class CommunicationEmailProvider extends EmailProvider
{
    public function send(Sendable $email)
    {
        /**
         * @var Email $email
         */

        $package = new CommunicationPackage();
        $package->addSendable($email);
        $package->title = $email->getSubject();
        $package->send();
    }
}
