<?php

namespace Rhubarb\Scaffolds\Communications\CommunicationPackages;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Sendables\Sendable;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;

class CommunicationPackage
{
    /**
     * @var string The title of the communication
     */
    public $title;

    /**
     * @var RhubarbDateTime The Date to send the communication
     */
    public $dateToSend;

    /**
     * @var Sendable[]
     */
    private $sendables = [];

    public function addSendable(Sendable $sendableEmail)
    {
        $this->sendables[] = $sendableEmail;
    }

    public function getSendables()
    {
        return $this->sendables;
    }

    public function send()
    {
        return CommunicationProcessor::sendPackage($this);
    }

    public function schedule($date = null)
    {
        return CommunicationProcessor::schedulePackage($this, $date);
    }

    public function draft()
    {
        return CommunicationProcessor::draftPackage($this);
    }
}
