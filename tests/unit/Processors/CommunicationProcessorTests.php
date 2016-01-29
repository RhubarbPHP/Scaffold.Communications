<?php

namespace Rhubarb\Scaffolds\Communications\Tests\Providers;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;
use Rhubarb\Scaffolds\Communications\Tests\Fixtures\CommunicationTestCase;

class CommunicationProcessorTests extends CommunicationTestCase
{
    public function testSendCommunication()
    {
        $communication = new Communication();
        $communication->Type = Communication::TYPE_EMAIL;
        $communication->ToRecipients = "test@test.com";
        $communication->FromSender = "sender@gcdtech.com";
        $communication->Body = "Test Message Body";

        $communication->save();

        $unitTestingCommunicationProcessor = new UnitTestingCommunicationProcessor($communication);
        $unitTestingCommunicationProcessor->sendCommunication();

        $this->assertTrue(UnitTestingCommunicationProcessor::$communicationSent);
        $this->assertTrue($communication->Sent);
    }

    public function testSendCommunicationWithFutureDate()
    {
        $communication = new Communication();
        $communication->Type = Communication::TYPE_EMAIL;
        $communication->ToRecipients = "test@test.com";
        $communication->FromSender = "sender@gcdtech.com";
        $communication->Body = "Test Message Body";
        $communication->DateToSend = new RhubarbDateTime("tomorrow");

        $communication->save();

        $unitTestingCommunicationProcessor = new UnitTestingCommunicationProcessor($communication);
        $unitTestingCommunicationProcessor->sendCommunication();

        $this->assertFalse(UnitTestingCommunicationProcessor::$communicationSent);
        $this->assertFalse($communication->Sent);
    }
}

class UnitTestingCommunicationProcessor extends CommunicationProcessor {

    static $communicationSent = false;

    protected function processCommunicationAfterSending()
    {
        parent::processCommunicationAfterSending();

        self::$communicationSent = true;
    }

}
