<?php
/**
 * @author michaelmiscampbell
 * Date: 29/01/2016
 */

namespace Rhubarb\Scaffolds\Communications\Tests\Processors;


use Rhubarb\Crown\Sendables\Email\EmailProvider;
use Rhubarb\Crown\Sendables\Email\SimpleEmail;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;
use Rhubarb\Scaffolds\Communications\EmailProviders\CommunicationEmailProvider;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationEmail;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;
use Rhubarb\Scaffolds\Communications\Tests\Fixtures\CommunicationTestCase;

class CommunicationEmailProviderTest extends CommunicationTestCase
{
    public function testCommunicationEmailProviderCreatesCommunications()
    {
        $this->assertCount(0, CommunicationEmail::find(), "CommunicationEmail count was not 0");
        $this->assertCount(0, Communication::find(), "Communication count was not 0");

        $email = new SimpleEmail();
        $email->setSubject("A fine day in the park");
        $email->addRecipient("test@test.com", "Joe Bloggs");
        $email->setHtml("<html><head>Test Head</head><body>Test Body</body></html>");
        $email->setText("Test Text Body");

        CommunicationProcessor::setEmailProviderClassName(UnitTestingEmailProvider::class);
        EmailProvider::setDefaultEmailProviderClassName(CommunicationEmailProvider::class);
        EmailProvider::getDefaultProvider()->sendEmail($email);

        $this->assertCount(1, Communication::find(), "Communication count was not 1");
        $this->assertCount(1, CommunicationEmail::find(), "CommunicationEmail count was not 1");

        $communication = Communication::findLast();
        $this->assertEquals($email->getSubject(), $communication->Name, "Subject not set correctly");
        $this->assertTrue($communication->DateCreated->isValidDateTime(), "Date Created not set correctly");

        $communicationEmail = CommunicationEmail::findLast();

        $this->assertEquals($communicationEmail->CommunicationID, $communication->CommunicationID);

        $this->assertEquals($email->getSubject(), $communicationEmail->Subject, "Subject not set correctly" );
        $this->assertEquals(current($email->getRecipients())->name, $communicationEmail->RecipientName, "Name not set correctly");
        $this->assertEquals(current($email->getRecipients())->email, $communicationEmail->RecipientEmail, "Name not set correctly");
        $this->assertEquals($email->getSender()->name, $communicationEmail->SenderName, "Sender email not set correctly");
        $this->assertEquals($email->getSender()->email, $communicationEmail->SenderEmail, "Sender email not set correctly");
        $this->assertEquals($email->getHtml(), $communicationEmail->HtmlBody, "Html Body not set correctly");
        $this->assertEquals($email->getText(), $communicationEmail->TextBody, "Text Body not set correctly");

        $email->addRecipient("jdoe@hotmail.com", "Jane Doe");

        EmailProvider::getDefaultProvider()->sendEmail($email);

        $this->assertCount(3, CommunicationEmail::find(), "CommunicationEmail count was not 3 (2 recipients in email)");

        $communicationEmail = CommunicationEmail::findLast();

        $this->assertEquals($email->getSubject(), $communicationEmail->Subject, "Subject not set correctly");
        $this->assertEquals("Jane Doe", $communicationEmail->RecipientName, "Name not set correctly");
        $this->assertEquals("jdoe@hotmail.com", $communicationEmail->RecipientEmail, "Name not set correctly");
    }
}
