<?php

namespace Rhubarb\Scaffolds\Communications\Tests\Providers;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Sendables\Email\EmailProvider;
use Rhubarb\Crown\Sendables\Email\SimpleEmail;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Scaffolds\Communications\CommunicationPackages\CommunicationPackage;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;
use Rhubarb\Scaffolds\Communications\Tests\Fixtures\CommunicationTestCase;

class CommunicationProcessorTest extends CommunicationTestCase
{
    protected function _before()
    {
        parent::_before();

        CommunicationProcessor::setProviderClassName(EmailProvider::class, UnitTestingEmailProvider::class);
    }

    public function testSendCommunication()
    {
        $communication = $this->createCommunication(null);

        $communication->save();

        CommunicationProcessor::sendCommunication($communication);

        $this->assertTrue($communication->Completed);
    }

    public function testSendCommunicationWithFutureDateToSend()
    {
        $communication = $this->createCommunication(new RhubarbDateTime("tomorrow"));

        $communication->save();

        CommunicationProcessor::sendCommunication($communication);

        $this->assertFalse($communication->Completed, "Communication sent before its Date to send date");
    }

    public function testSendCommunicationWithCurrentDateToSend()
    {
        $communication = $this->createCommunication(new RhubarbDateTime("now"));

        CommunicationProcessor::sendCommunication($communication);

        $this->assertTrue($communication->Completed);
    }

    public function testSendEmailCommunication()
    {
        $communication = $this->createCommunication(new RhubarbDateTime("now"));

        CommunicationProcessor::sendCommunication($communication);

        $this->assertNotNull(UnitTestingEmailProvider::GetLastEmail(), "Email object not generated");
    }

    public function testSendEmailCommunicationTwice()
    {
        $communication = $this->createCommunication(new RhubarbDateTime("now"));

        CommunicationProcessor::sendCommunication($communication);

        $lastEmail = UnitTestingEmailProvider::GetLastEmail();

        CommunicationProcessor::sendCommunication($communication);

        $this->assertSame($lastEmail, UnitTestingEmailProvider::GetLastEmail(), "Email should not have been sent again");
    }

    public function testSendEmailWithFailedAttempts()
    {
        $noOfRecipients = 6;
        $communication = $this->createCommunicationWithMultipleRecipients(new RhubarbDateTime("now"), $noOfRecipients);

        $communicationEmail = $communication->Items[$noOfRecipients - 1];
        $communicationEmail->Sent = true;
        $communicationEmail->save();

        CommunicationProcessor::sendCommunication($communication);

        $this->assertCount(1, UnitTestingEmailProvider::GetLastEmail()->getRecipients(),
            "Communication scaffold should have unwrapped the recipients to individual email items. Each email item".
            " should have just 1 recipient");

        $this->assertEquals("test@test5.com", current(UnitTestingEmailProvider::GetLastEmail()->getRecipients())->email);
    }

    /**
     * @return CommunicationItem
     */
    private function createCommunication($dateToSend)
    {
        $email = new SimpleEmail();
        $email->setText("Test Message Body");
        $email->setSender("sender@gcdtech.com");
        $email->addRecipientByEmail("test@test.com");

        $package = new CommunicationPackage();
        $package->addSendable($email);
        $package->send();

        $communication = Communication::findLast();

        if ($dateToSend) {
            $communication->DateToSend = $dateToSend;
        }
        return $communication;
    }

    private function createCommunicationWithMultipleRecipients($dateToSend, $noOfRecipients)
    {
        $email = new SimpleEmail();
        $email->setText("Test Message Body");
        $email->setSender("sender@gcdtech.com");
        foreach (range(1, $noOfRecipients) as $i) {
            $email->addRecipientByEmail("test@test" . $i . ".com");
        }

        $package = new CommunicationPackage();
        $package->addSendable($email);
        $package->send();

        $communication = Communication::findLast();

        if ($dateToSend) {
            $communication->DateToSend = $dateToSend;
        }

        return $communication;
    }
}
