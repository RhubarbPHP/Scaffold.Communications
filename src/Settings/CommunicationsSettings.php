<?php

namespace Rhubarb\Scaffolds\Communications\Settings;
use Rhubarb\Scaffolds\ApplicationSettings\Settings\ApplicationSettings;

/**
 * Class CommunicationsSettings
 *
 * @property bool $communicationsEmailSending
 */
class CommunicationsSettings extends ApplicationSettings
{
    /**
     * @var bool
     */
    public static $showSendAllCommunicationsButton = false;
}
