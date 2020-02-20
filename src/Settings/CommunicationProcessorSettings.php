<?php

namespace Rhubarb\Scaffolds\Communications\Settings;

use Rhubarb\Crown\Settings;

class CommunicationProcessorSettings extends Settings
{
    /**
     * @var int Number of emails to send max per second.
     */
    public $throttle = 20;

    public $storeHtml = false;
}
