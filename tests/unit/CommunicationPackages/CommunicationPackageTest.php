<?php

namespace Rhubarb\Scaffolds\Communications\Tests\CommunicationPackages;

use Rhubarb\Crown\Sendables\Email\SimpleEmail;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Scaffolds\Communications\CommunicationPackages\CommunicationPackage;
use Rhubarb\Scaffolds\Communications\Exceptions\SendingDraftCommunicationException;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;
use Rhubarb\Scaffolds\Communications\Tests\Fixtures\CommunicationTestCase;

class CommunicationPackageTest extends CommunicationTestCase
{
    /**
     * @var CommunicationPackage
     */
    private $package;

    protected function setUp()
    {
        parent::setUp();

        $this->package = new CommunicationPackage();
    }

    public function testPackageContainsSendables()
    {
        $this->createEmailAndAddToPackage();

        $this->assertCount(1, $this->package->getSendables(), "The sendable wasn't correctly added to the list");

        $this->createEmailAndAddToPackage();

        $this->assertCount(2, $this->package->getSendables(), "The sendable wasn't correctly added to the list");
    }

    public function testDraftPackageCantSend()
    {
        $email = $this->createEmailAndAddToPackage();
        $email->addRecipientByEmail("test@test.com");
        $email->setSubject("A test email");
        $email->setText("This is the end, my lonely friend, the end.");

        $this->package->title = "A test communication";
        $communication = $this->package->draft();

        $sent = CommunicationProcessor::sendCommunication($communication);

        $this->assertFalse($sent, "Draft communications shouldn't send");
    }

    public function testPackageSends()
    {
        $email = $this->createEmailAndAddToPackage();
        $email->addRecipientByEmail("test@test.com");
        $email->setSubject("A test email");
        $email->setText("This is the end, my lonely friend, the end.");

        $this->package->title = "A test communication";
        $this->package->send();

        $this->assertCount(1, Communication::find() );

        $communication = Communication::findLast();

        $this->assertEquals($this->package->title, $communication->Title, "The communication wasn't titled properly");

        $this->assertCount(1, CommunicationItem::find());

        $item = CommunicationItem::findLast();

        $this->assertEquals("test@test.com", $item->Recipient, "The item didn't have a recipient");
        $this->assertEquals("Email", $item->Type, "The type should have been email");
        $this->assertEquals($email->getText(), $item->Text, "The text value wasn't set correctly");
        $this->assertEquals(get_class($email), $item->SendableClassName, "The class name wasn't set correctly");
        $this->assertEquals($email->toArray(), $item->Data, "The sendable wasn't encoded into data correctly");
        $this->assertEquals($communication->UniqueIdentifier, $item->CommunicationID,
            "The item wasn't attached to the communication properly");

        $lastEmail = UnitTestingEmailProvider::getLastEmail();

        $this->assertEquals("A test email", $lastEmail->getSubject(), "The email should actually have been sent as it wasn't delayed");
    }

    public function testPackageWithMultipleSendables()
    {
        $email = $this->createEmailAndAddToPackage();
        $email->addRecipientByEmail("test@test.com");
        $email->setSubject("A test email");
        $email->setText("This is the end, my lonely friend, the end.");

        $email = $this->createEmailAndAddToPackage();
        $email->addRecipientByEmail("test@test.com");
        $email->setSubject("A second test email");
        $email->setText("This is the end, my lonely friend, the end.");

        $this->package->title = "A test communication";
        $this->package->send();

        $this->assertCount(2, CommunicationItem::find());
    }

    public function testPackageIsActuallySent()
    {

    }

    /**
     * @return SimpleEmail
     */
    private function createEmailAndAddToPackage()
    {
        $sendableEmail = new SimpleEmail();
        $this->package->addSendable($sendableEmail);
        return $sendableEmail;
    }

    public function testPackageCanBeRecordedNotSent()
    {
        $lastEmail = UnitTestingEmailProvider::getLastEmail();

        $email = new SimpleEmail();
        $package = new CommunicationPackage();
        $package->title = "Test With No Send";
        $package->addSendable($email);
        $package->recordSent();

        $communication = Communication::findLast();

        $this->assertEquals("Sent", $communication->Status);
        $this->assertEquals($lastEmail, UnitTestingEmailProvider::getLastEmail());
    }
}
