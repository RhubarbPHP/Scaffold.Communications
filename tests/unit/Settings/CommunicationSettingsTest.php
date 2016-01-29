<?php

namespace Rhubarb\Scaffolds\Communications\Tests\Settings;

use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;
use Rhubarb\Scaffolds\Communications\Settings\CommunicationSettings;
use Rhubarb\Scaffolds\Communications\Tests\Fixtures\CommunicationTestCase;

class CommunicationSettingsTest extends CommunicationTestCase
{
    protected function _before()
    {
        parent::_before();

        $communicationSettings = new CommunicationSettings();
        $communicationSettings->EmailProviderClass = UnitTestingEmailProvider::class;
    }

    public function testProviderForSettings()
    {
        $communication = new Communication();
        $communication->Type = Communication::TYPE_EMAIL;
        $communication->ToRecipients = "test@test.com";
        $communication->FromSender = "sender@gcdtech.com";
        $communication->Body = "Test Message Body";

        $communication->save();

        $communicationProcessor = new CommunicationProcessor($communication);
        $communicationProcessor->sendCommunication();

        $this->assertTrue($communication->Sent);
    }
}
