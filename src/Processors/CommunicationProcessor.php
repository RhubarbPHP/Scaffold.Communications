<?php

namespace Rhubarb\Scaffolds\Communications\Processors;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Sendables\Email\SimpleEmail;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Scaffolds\Communications\CommunicationPackages\CommunicationPackage;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;

/**
 * Class CommunicationProcessor
 * @package Rhubarb\Scaffolds\Communications\Processors
 */
final class CommunicationProcessor
{
    private static $emailProviderClassName;

    final public static function sendCommunication(Communication $communication)
    {
        Log::debug("Considering to send CommunicationItem with ID: " . $communication->CommunicationID, "COMMS");

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
        foreach($communication->Items as $email){
            self::sendEmail($email);
        }
    }

    final private static function sendEmail(CommunicationItem $communicationEmail)
    {
        if ($communicationEmail->Sent){
            Log::warning("Attempt blocked to send already sent email", "COMMS",
                [
                    "CommunicationItemID" => $communicationEmail->CommunicationItemID,
                    "EmailProvider" => self::$emailProviderClassName
                ]
            );
            return;
        }

        $emailProvider = self::getEmailProvider();
        $emailProvider->send($communicationEmail->getEmail());

        $communicationEmail->Sent = true;
        $communicationEmail->save();

        Log::debug("Sending communication by Email", "COMMS", ["CommunicationID" => $communicationEmail->CommunicationID,
        "EmailProvider" => self::$emailProviderClassName]);
    }

    public static function setEmailProviderClassName($emailProviderClassName)
    {
        self::$emailProviderClassName = $emailProviderClassName;
    }

    public static function getEmailProvider()
    {
        $class = self::$emailProviderClassName;
        return new $class();
    }

    public static function sendPackage(CommunicationPackage $package)
    {
        $communication = new Communication();
        $communication->Title = $package->title;
        $communication->save();

        $item = new CommunicationItem();
        $item->Recipient = current($package->getSendables()[0]->getRecipients())->email;
        $item->TextBody = "asdf";
        $item->Type = "123";
        $item->save();
    }
}
