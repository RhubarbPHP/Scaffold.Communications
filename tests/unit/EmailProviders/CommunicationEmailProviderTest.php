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
use Rhubarb\Scaffolds\Communications\Exceptions\InvalidProviderException;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Scaffolds\Communications\Processors\CommunicationProcessor;
use Rhubarb\Scaffolds\Communications\Tests\Fixtures\CommunicationTestCase;

class CommunicationEmailProviderTest extends CommunicationTestCase
{
    public function testCommunicationEmailProviderCreatesCommunications()
    {
        $this->assertCount(0, CommunicationItem::find(), "CommunicationItem count was not 0");
        $this->assertCount(0, Communication::find(), "Communication count was not 0");

        $email = new SimpleEmail();
        $email->setSubject("A fine day in the park");
        $email->addRecipientByEmail("test@test.com", "Joe Bloggs");
        $email->setHtml("<html><head>Test Head</head><body>Test Body</body></html>");
        $email->setText("Test Text Body");

        CommunicationProcessor::setProviderClassName(EmailProvider::class, UnitTestingEmailProvider::class);

        EmailProvider::setProviderClassName(CommunicationEmailProvider::class);
        EmailProvider::getProvider()->send($email);

        $this->assertCount(1, Communication::find(), "Communication count was not 1");
        $this->assertCount(1, CommunicationItem::find(), "CommunicationItem count was not 1");

        $communication = Communication::findLast();
        $this->assertEquals($email->getSubject(), $communication->Title, "Subject not set correctly");
        $this->assertTrue($communication->DateCreated->isValidDateTime(), "Date Created not set correctly");

        $communicationEmail = CommunicationItem::findLast();

        $this->assertEquals($communicationEmail->CommunicationID, $communication->CommunicationID);

        $this->assertEquals($email->getRecipients()[0]->__toString(), $communicationEmail->Recipient, "Name not set correctly");
        $this->assertEquals($email->getText(), $communicationEmail->Text, "Text Body not set correctly");

        $email->addRecipientByEmail("jdoe@hotmail.com", "Jane Doe");

        EmailProvider::getProvider()->send($email);

        $this->assertCount(3, CommunicationItem::find(), "CommunicationItem count was not 3 (2 recipients in email)");

        $communicationEmail = CommunicationItem::findLast();

        $this->assertEquals('"Jane Doe" <jdoe@hotmail.com>', $communicationEmail->Recipient, "Recipient not set correctly");
    }

    public function testCommunicationEmailProviderCantBeAProcessor()
    {
        $this->assertCount(0, CommunicationItem::find(), "CommunicationItem count was not 0");
        $this->assertCount(0, Communication::find(), "Communication count was not 0");

        $email = new SimpleEmail();
        $email->setSubject("A fine day in the park");
        $email->addRecipientByEmail("test@test.com", "Joe Bloggs");
        $email->setHtml("<html><head>Test Head</head><body>Test Body</body></html>");
        $email->setText("Test Text Body");

        CommunicationProcessor::setProviderClassName(EmailProvider::class, CommunicationEmailProvider::class);

        try {
            EmailProvider::setProviderClassName(CommunicationEmailProvider::class);
            EmailProvider::getProvider()->send($email);
            $this->fail('Email should not sent due to invalid Communication Provider being set');
        } catch (InvalidProviderException $exception) {
        }
    }
}
