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

        $this->createEmailAndAddToPackage($this->package);

        $this->assertCount(2, $this->package->getSendables(), "The sendable wasn't correctly added to the list");
    }

    public function testPackageSends()
    {
        $email = $this->createEmailAndAddToPackage();
        $email->addRecipient("test@test.com");
        $email->setSubject("A test email");

        $this->package->title = "A test communication";
        $this->package->send();

        $this->assertCount(1, Communication::find() );

        $communication = Communication::findLast();

        $this->assertEquals($this->package->title, $communication->Title, "The communication wasn't titled properly");

        $this->assertCount(1, CommunicationItem::find());

        $item = CommunicationItem::findLast();

        $this->assertEquals("test@test.com", $item->Recipient, "The item didn't have a recipient");
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
