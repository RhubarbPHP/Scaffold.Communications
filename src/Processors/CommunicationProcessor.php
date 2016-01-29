<?php

namespace Rhubarb\Scaffolds\Communications\Processors;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Scaffolds\Communications\Models\Communication;

/**
 * Class CommunicationProcessor
 * @package Rhubarb\Scaffolds\Communications\Processors
 */
class CommunicationProcessor
{
    /**
     * @var Communication
     */
    private $communication;

    public function __construct(Communication $communication)
    {
        $this->communication = $communication;
    }

    final public function sendCommunication()
    {
        if ($this->validateCommunication())
        {
            switch ($this->communication->Type) {
                case Communication::TYPE_BLANK:
                    break;
                case Communication::TYPE_EMAIL:
                    $this->sendEmail();
                    break;
            }
        }
    }

    final private function validateCommunication() {
        return $this->communication->shouldSendCommunication();
    }

    final private function sendEmail()
    {
        $this->communication->Sent = true;
        $this->communication->save();

        $this->processCommunicationAfterSending();
    }

    protected function processCommunicationAfterSending() {

    }
}
