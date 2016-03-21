<?php

namespace Rhubarb\Scaffolds\Communications\Tests\CommunicationPackages;

use Rhubarb\Crown\Sendables\Email\SimpleEmail;
use Rhubarb\Scaffolds\Communications\CommunicationPackages\CommunicationPackage;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
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

    public function testPackageSends()
    {
        $email = $this->createEmailAndAddToPackage();
        $email->addRecipient("test@test.com");
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


    }

    public function testPackageWithMultipleSendables()
    {
        $email = $this->createEmailAndAddToPackage();
        $email->addRecipient("test@test.com");
        $email->setSubject("A test email");
        $email->setText("This is the end, my lonely friend, the end.");

        $email = $this->createEmailAndAddToPackage();
        $email->addRecipient("test@test.com");
        $email->setSubject("A second test email");
        $email->setText("This is the end, my lonely friend, the end.");

        $this->package->title = "A test communication";
        $this->package->send();

        $this->assertCount(2, CommunicationItem::find());
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
}
