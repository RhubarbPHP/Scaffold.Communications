<?php

namespace Rhubarb\Scaffolds\Communications\Processors;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Email\SimpleEmail;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationEmail;

/**
 * Class CommunicationProcessor
 * @package Rhubarb\Scaffolds\Communications\Processors
 */
final class CommunicationProcessor
{
    private static $emailProviderClassName;

    final public static function sendCommunication(Communication $communication)
    {
        Log::debug("Considering to send CommunicationEmail with ID: " . $communication->CommunicationID, "COMMS");

        $currentDateTime = new RhubarbDateTime("now");

        if ($communication->shouldSendCommunication($currentDateTime))
        {
            self::sendEmails($communication);

            $communication->Completed = true;
            $communication->save();
        }
    }

    final private static function sendEmails(Communication $communication)
    {
        foreach($communication->Emails as $email){
            self::sendEmail($email);
        }
    }

    final private static function sendEmail(CommunicationEmail $communicationEmail)
    {
        if ($communicationEmail->Sent){
            Log::warning("Attempt blocked to send already sent email", "COMMS",
                [
                    "CommunicationEmailID" => $communicationEmail->CommunicationEmailID,
                    "EmailProvider" => self::$emailProviderClassName
                ]
            );
            return;
        }

        $emailProvider = new self::$emailProviderClassName();
        $emailProvider->sendEmail($communicationEmail->getEmail());

        $communicationEmail->Sent = true;
        $communicationEmail->save();

        Log::debug("Sending communication by Email", "COMMS", ["CommunicationID" => $communicationEmail->CommunicationID,
        "EmailProvider" => self::$emailProviderClassName]);
    }

    public static function setEmailProviderClassName($emailProviderClassName)
    {
        self::$emailProviderClassName = $emailProviderClassName;
    }
}
