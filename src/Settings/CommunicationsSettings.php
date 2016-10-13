<?php

namespace Rhubarb\Scaffolds\Communications\Settings;

use Rhubarb\Crown\Settings;

/**
 * Class CommunicationsSettings
 *
 * @property bool $communicationsEmailSending
 */
class CommunicationsSettings extends Settings
{
    /**
     * @var bool
     */
    public static $showSendAllCommunicationsButton = false;
}
