<?php

namespace Rhubarb\Scaffolds\Communications\EmailProviders;

use Rhubarb\Crown\Email\Email;
use Rhubarb\Crown\Email\EmailProvider;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationEmail;

class CommunicationEmailProvider extends EmailProvider
{
    public function sendEmail(Email $email)
    {
        Communication::fromEmail($email);
    }
}
