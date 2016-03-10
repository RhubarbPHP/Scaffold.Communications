<?php

namespace Rhubarb\Scaffolds\Communications\CommunicationPackages;

use Rhubarb\Crown\Sendables\Sendable;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;

class CommunicationPackage
{
    /**
     * @var string The title of the communication
     */
    public $title;

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
        CommunicationProcessor::sendPackage($this);
    }
}
