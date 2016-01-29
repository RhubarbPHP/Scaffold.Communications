<?php

namespace Rhubarb\Scaffolds\Communications\Tests\Models;

use Rhubarb\Scaffolds\Communications\Models\CommunicationEmail;
use Rhubarb\Scaffolds\Communications\Tests\Fixtures\CommunicationTestCase;

class CommunicationEmailTest extends CommunicationTestCase
{
    public function testEmailExtractionFromCommunicationEmail()
    {
        $communicationEmail = new CommunicationEmail();
        $communicationEmail->Subject = "The three billy goats";
        $communicationEmail->RecipientName = "John Smith";
        $communicationEmail->RecipientEmail = "john.smith@outlook.com";
        $communicationEmail->SenderName = "Jane Smith";
        $communicationEmail->SenderEmail = "jane.smith@outlook.com";
        $communicationEmail->TextBody = "Michael went to mow, went to mow a meadow.";
        $communicationEmail->HtmlBody = "<p>Michael went to mow, went to mow a meadow.</p>";
        $communicationEmail->addAttachment("file_location.txt");
        $communicationEmail->addAttachment("file_location1.txt", "TestFakeFileName");
        $communicationEmail->save();

        $email = $communicationEmail->getEmail();

        $this->assertEquals($communicationEmail->Subject, $email->getSubject(), "Subjects don't match");
        $this->assertEquals($communicationEmail->RecipientName, current($email->getRecipients())->name, "RecipientName don't match");
        $this->assertEquals($communicationEmail->RecipientEmail, current($email->getRecipients())->email, "RecipientEmail don't match");
        $this->assertEquals($communicationEmail->SenderName, $email->getSender()->name, "SenderName don't match");
        $this->assertEquals($communicationEmail->SenderEmail,$email->getSender()->email, "RecipientEmail don't match");
        $this->assertEquals($communicationEmail->TextBody,$email->getText(), "TextBody don't match");
        $this->assertEquals($communicationEmail->HtmlBody,$email->getHtml(), "HtmlBody don't match");
        $attachments = json_decode($communicationEmail->Attachments);
        $this->assertEquals($attachments, $email->getAttachments(), "Attachments don't match");
    }
}
