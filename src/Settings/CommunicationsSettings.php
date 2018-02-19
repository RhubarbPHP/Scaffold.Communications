<?php

namespace Rhubarb\Scaffolds\Communications\Settings;
use Rhubarb\Scaffolds\ApplicationSettings\Settings\ApplicationSettings;
use Rhubarb\Scaffolds\Communications\Decorators\CommunicationDecorator;

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

    public static $defaultDateTimeFormat = CommunicationDecorator::DATE_FORMAT;
}
