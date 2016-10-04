<?php

namespace Rhubarb\Scaffolds\Communications\Processors;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\Sendables\Email\SimpleEmail;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Scaffolds\Communications\BackgroundTasks\CommunicationBackgroundTask;
use Rhubarb\Scaffolds\Communications\CaptureToCommunciationsProcessorInterface;
use Rhubarb\Scaffolds\Communications\CommunicationPackages\CommunicationPackage;
use Rhubarb\Scaffolds\Communications\EmailProviders\CommunicationEmailProvider;
use Rhubarb\Scaffolds\Communications\Exceptions\InvalidProviderException;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Stem\Schema\SolutionSchema;

/**
 * Class CommunicationProcessor
 * @package Rhubarb\Scaffolds\Communications\Processors
 */
final class CommunicationProcessor
{
    private static $emailProviderClassName;

    private static $container;

    private static function getContainer()
    {
        if (self::$container == null){
            self::$container = new Container();
        }

        return self::$container;
    }

    final public static function sendCommunication(Communication $communication)
    {
        Log::debug("Considering to send CommunicationItem with ID: " . $communication->CommunicationID, "COMMS");

        $currentDateTime = new RhubarbDateTime("now");

        if ($communication->shouldSendCommunication($currentDateTime))
        {
            self::sendItems($communication);

            $communication->Completed = true;
            $communication->save();
        }
    }

    final private static function sendItems(Communication $communication)
    {
        foreach($communication->Items as $item){
            self::sendItem($item);
        }
    }

    final private static function sendItem(CommunicationItem $item)
    {
        if ($item->Sent){
            Log::warning("Attempt blocked to send already sent email", "COMMS",
                [
                    "CommunicationItemID" => $item->CommunicationItemID,
                    "EmailProvider" => self::$emailProviderClassName
                ]
            );
            return;
        }

        $sendable = $item->getSendable();
        $providerClass = $sendable->getProviderClassName();
        $provider = self::getContainer()->getInstance($providerClass);

        if ($provider instanceof CaptureToCommunciationsProcessorInterface) {
            throw new InvalidProviderException();
        }

        $provider->send($sendable);

        $item->Sent = true;
        $item->save();

        Log::debug("Sending communication by Email", "COMMS", ["CommunicationID" => $item->CommunicationID,
        "EmailProvider" => self::$emailProviderClassName]);
    }

    public static function setProviderClassName($sendableProviderBaseClassName, $concreteProviderClassName)
    {
        self::getContainer()->registerClass($sendableProviderBaseClassName, $concreteProviderClassName);
    }

    public static function sendPackage(CommunicationPackage $package)
    {
        $communication = SolutionSchema::getModel("Communication");
        $communication->Title = $package->title;
        if ($package->dateToSend) {
            $communication->DateToSend = $package->dateToSend;
        }
        $communication->save();

        foreach($package->getSendables() as $sendable) {
            foreach($sendable->getRecipients() as $recipient ) {

                $clone = clone $sendable;
                $clone->clearRecipients();
                $clone->addRecipient($recipient);

                $item = new CommunicationItem();
                $item->Recipient = (string) $recipient;
                $item->Text = $clone->getText();
                $item->Type = $clone->getSendableType();
                $item->SendableClassName = get_class($clone);
                $item->Data = $clone->toArray();
                $item->CommunicationID = $communication->CommunicationID;
                $item->save();
            }
        }

        self::sendCommunication($communication);

        return $communication;
    }
}
