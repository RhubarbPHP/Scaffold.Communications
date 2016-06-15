<?php

namespace Rhubarb\Scaffolds\Communications\EmailProviders;

use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Crown\Sendables\Email\EmailProvider;
use Rhubarb\Crown\Sendables\Sendable;
use Rhubarb\Scaffolds\Communications\CommunicationPackages\CommunicationPackage;

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
