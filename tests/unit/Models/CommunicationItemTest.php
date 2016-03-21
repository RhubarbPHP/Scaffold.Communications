<?php

namespace Rhubarb\Scaffolds\Communications\Tests\Models;

use Rhubarb\Crown\Sendables\Email\SimpleEmail;
use Rhubarb\Scaffolds\Communications\CommunicationPackages\CommunicationPackage;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Scaffolds\Communications\Tests\Fixtures\CommunicationTestCase;

class CommunicationItemTest extends CommunicationTestCase
{
    public function testEmailExtractionFromCommunicationEmail()
    {
        $email = new SimpleEmail();
        $email->addRecipient("John Smith", "john.smith@outlook.com");
        $email->setSender("Jane Smith", "jane.smith@outlook.com");
        $email->setSubject("The three billy goats");
        $email->setText("Michael went to mow, went to mow a meadow.");
        $email->setHtml("<p>Michael went to mow, went to mow a meadow.</p>");
        $email->addAttachment("file_location.txt");
        $email->addAttachment("file_location1.txt", "TestFakeFileName");

        $package = new CommunicationPackage();
        $package->addSendable($email);
        $package->title = $email->getSubject();
        $package->send();

        $item = CommunicationItem::findLast();

        $derivedEmail = $item->getSendable();

        $this->assertEquals($email, $derivedEmail, "The derived email isn't exactly the same as the original");
        $this->assertInstanceOf(SimpleEmail::class, $derivedEmail);

        // Next test is that a different sendable generates the correct type when getSendable is called.
    }
}
