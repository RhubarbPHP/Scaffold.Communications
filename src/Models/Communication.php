<?php

namespace Rhubarb\Scaffolds\Communications\Models;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Filters\Not;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\BooleanColumn;
use Rhubarb\Stem\Schema\Columns\DateTimeColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 * @property int $CommunicationID
 * @property \DateTime $DateCreated
 * @property string Title
 * @property \DateTime $DateCompleted
 * @property bool $Completed
 */
class Communication extends Model
{
    public static function fromEmail(Email $email)
    {
        $communication = new Communication();
        $communication->Title = $email->getSubject();
        $communication->save();

        foreach ($email->getRecipients() as $recipient) {
            $communicationEmail = new CommunicationItem();
            $communicationEmail->RecipientName = $recipient->name;
            $communicationEmail->RecipientEmail = $recipient->email;
            $communicationEmail->SenderName = $email->getSender()->name;
            $communicationEmail->SenderEmail = $email->getSender()->email;
            $communicationEmail->HtmlBody = $email->getHtml();
            $communicationEmail->Text = $email->getText();
            $communicationEmail->Subject = $email->getSubject();
            $communicationEmail->Attachments = $email->getAttachments();

            $communication->Items->append($communicationEmail);
        }

        return $communication;
    }

    public function setCompleted($newValue)
    {
        $this->setModelValue("Completed", $newValue);
        $this->setModelValue("DateCompleted", new RhubarbDateTime("now"));
    }

    public function setDateCompleted($newValue)
    {
        throw new ModelConsistencyValidationException();
    }

    protected function createSchema()
    {
        $schema = new ModelSchema("tblCommunication");

        $schema->addColumn(
            new AutoIncrementColumn("CommunicationID"),
            new StringColumn("Title", 150),
            new DateTimeColumn("DateCreated"),
            new DateTimeColumn("DateCompleted"),
            new DateTimeColumn("DateToSend"),
            new BooleanColumn("Completed", false)
        );

        return $schema;
    }

    /**
     * True if the communication can now be sent.
     *
     * @param RhubarbDateTime $currentDateTime
     * @return bool
     */
    public function shouldSendCommunication(RhubarbDateTime $currentDateTime)
    {
        if ($this->Completed) {
            return false;
        }

        if ($this->DateToSend && $this->DateToSend->isValidDateTime()) {
            if ($currentDateTime < $this->DateToSend) {
                return false;
            }
        }

        return true;
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord()){
            $this->DateCreated = "now";
        }

        parent::beforeSave();
    }

    public static function FindUnsentCommunications() {
        return self::Find( new AndGroup(
            [
                new Equals("Completed", false)
            ]
        ));
    }
}
