<?php

namespace Rhubarb\Scaffolds\Communications\Processors;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\String\StringTools;
use Rhubarb\Scaffolds\Communications\CaptureToCommunicationsProcessorInterface;
use Rhubarb\Scaffolds\Communications\CommunicationPackages\CommunicationPackage;
use Rhubarb\Scaffolds\Communications\CommunicationsModule;
use Rhubarb\Scaffolds\Communications\Exceptions\InvalidProviderException;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Schema\SolutionSchema;

final class CommunicationProcessor
{
    private static $emailProviderClassName;

    private static $container;

    private static function getContainer()
    {
        if (self::$container == null) {
            self::$container = new Container();
        }

        return self::$container;
    }

    final public static function sendCommunication(Communication $communication, $ignoreTime = false)
    {
        Log::debug("Considering to send CommunicationItem with ID: " . $communication->CommunicationID, "COMMS");

        $currentDateTime = new RhubarbDateTime("now");

        if (CommunicationsModule::isEmailSendingEnabled() && ($ignoreTime || $communication->shouldSendCommunication($currentDateTime))) {
            if (self::sendItems($communication)) {
                $communication->markSent();
            } else {
                $communication->Status =
                    Communication::STATUS_FAILED;
            }
            $communication->save();
            return true;
        } else {
            return false;
        }
    }

    final private static function sendItems(Communication $communication)
    {
        $success = true;
        foreach ($communication->Items as $item) {
            if (!self::sendItem($item)) {
                $success = false;
            }
        }
        return $success;
    }

    final private static function sendItem(CommunicationItem $item)
    {
        if ($item->Sent) {
            Log::warning(
                "Attempt blocked to send already sent email",
                "COMMS",
                [
                    "CommunicationItemID" => $item->CommunicationItemID,
                    "EmailProvider" => self::$emailProviderClassName
                ]
            );
            return true;
        }

        $sendable = $item->getSendable();
        if (!$sendable) {
            Log::warning(
                "Couldn't generate a sendable object",
                "COMMS",
                [
                    "CommunicationItemID" => $item->CommunicationItemID,
                    "EmailProvider" => self::$emailProviderClassName
                ]
            );

            $item->Status = CommunicationItem::STATUS_FAILED;
            $item->save();

            return false;
        }

        $providerClass = $sendable->getProviderClassName();
        $provider = self::getContainer()->getInstance($providerClass);

        if ($provider instanceof CaptureToCommunicationsProcessorInterface) {
            throw new InvalidProviderException();
        }

        try {
            $item->ProviderMessageID = $provider->send($sendable) ?? "";
            $item->markSent();

            Log::debug("Sent communication by Email", "COMMS", [
                "CommunicationID" => $item->CommunicationID,
                "CommunicationItemID" => $item->CommunicationItemID,
                "EmailProvider" => self::$emailProviderClassName
            ]);
        } catch (\Exception $exception) {
            $className = get_class($exception);
            if (strpos($className, '\\') !== false) {
                $className = StringTools::getShortClassNameFromNamespace($className);
            }
            $item->FailureReason = $className . ': ' . $exception->getMessage();
            $item->Status = CommunicationItem::STATUS_FAILED;

            Log::debug("Failed sending communication by Email", "COMMS", [
                "CommunicationID" => $item->CommunicationID,
                "CommunicationItemID" => $item->CommunicationItemID,
                "EmailProvider" => self::$emailProviderClassName,
                "ExceptionType" => get_class($exception),
                "ExceptionMessage" => $exception->getMessage()
            ]);
        }

        $item->save();

        return $item->Status == CommunicationItem::STATUS_SENT;
    }

    public static function setProviderClassName($sendableProviderBaseClassName, $concreteProviderClassName)
    {
        self::getContainer()->registerClass($sendableProviderBaseClassName, $concreteProviderClassName);
    }

    public static function schedulePackage(CommunicationPackage $package)
    {
        $communication = self::draftPackage($package);
        $communication->Status = "Scheduled";
        $communication->save();

        return $communication;
    }

    public static function sendPackage(CommunicationPackage $package)
    {
        $communication = self::schedulePackage($package);

        self::sendCommunication($communication);

        return $communication;
    }

    public static function draftPackage(CommunicationPackage $package)
    {
        $communication = SolutionSchema::getModel("Communication");
        $communication->Title = $package->title;
        if ($package->dateToSend) {
            $communication->DateToSend = $package->dateToSend;
        }
        $communication->save();

        foreach ($package->getSendables() as $sendable) {
            foreach ($sendable->getRecipients() as $recipient) {
                $clone = clone $sendable;
                $clone->clearRecipients();
                $clone->addRecipient($recipient);

                $item = SolutionSchema::getModel("CommunicationItem");
                $item->Recipient = (string)$recipient;
                $item->Text = $clone->getText();
                $item->Type = $clone->getSendableType();
                $item->SendableClassName = get_class($clone);
                $data = $clone->toArray();
                $item->save();
                $data["CommunicationItemID"] = $item->UniqueIdentifier;
                $item->Data = $data;
                $item->CommunicationID = $communication->CommunicationID;
                $item->save();
            }
        }

        return $communication;
    }
}
