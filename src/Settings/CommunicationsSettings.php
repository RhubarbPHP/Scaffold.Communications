<?php

namespace Rhubarb\Scaffolds\Communications\Settings;

use Rhubarb\Scaffolds\ApplicationSettings\Models\ApplicationSetting;

/**
 * Class CommunicationsSettings
 *
 * @property bool $communicationsEmailSending
 */
class CommunicationsSettings extends ApplicationSetting
{
    /**
     * @var bool
     */
    public $showSendAllCommunicationsButton = false;

    public $communicationsEmailSending = true;
}
