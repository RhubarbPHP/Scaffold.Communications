<?php

namespace Rhubarb\Scaffolds\Communications\Models;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Crown\Sendables\Email\SimpleEmail;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\BooleanColumn;
use Rhubarb\Stem\Schema\Columns\DateTimeColumn;
use Rhubarb\Stem\Schema\Columns\ForeignKeyColumn;
use Rhubarb\Stem\Schema\Columns\JsonColumn;
use Rhubarb\Stem\Schema\Columns\LongStringColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 * @property int $CommunicationEmailID
 * @property int $CommunicationID
 * @property string $RecipientName
 * @property string $RecipientEmail
 * @property string $SenderName
 * @property string $SenderEmail
 * @property string $Subject
 * @property string $HtmlBody
 * @property string $TextBody
 * @property string $Attachments
 * @property RhubarbDateTime $DateCreated
 * @property RhubarbDateTime $DateSent
 * @property bool $Sent
 */
class CommunicationEmail extends Model
{
    protected function createSchema()
    {
        $schema = new ModelSchema("tblCommunicationEmail");

        $schema->addColumn(
            new AutoIncrementColumn("CommunicationEmailID"),
            new ForeignKeyColumn("CommunicationID"),
            new StringColumn("RecipientName", 70),
            new StringColumn("RecipientEmail", 200),
            new StringColumn("SenderName", 100),
            new StringColumn("SenderEmail", 100),
            new StringColumn("Subject", 160),
            new LongStringColumn("HtmlBody"),
            new LongStringColumn("TextBody"),
            new JsonColumn("Attachments"),
            new DateTimeColumn("DateCreated"),
            new DateTimeColumn("DateSent"),
            new BooleanColumn("Sent", false)
        );

        return $schema;
    }

    public function setSent($newValue)
    {
        $this->setModelValue("Sent", $newValue);
        $this->setModelValue("DateSent", new RhubarbDateTime("now"));
    }

    public function setDateSent($newValue)
    {
        throw new ModelConsistencyValidationException();
    }

    protected function getConsistencyValidationErrors()
    {
        $validationErrors = parent::getConsistencyValidationErrors();

        if (empty($this->RecipientEmail)) {
            $validationErrors["RecipientEmail"] = "RecipientEmail field cannot be empty";
        }

        if (empty($this->SenderEmail)) {
            $validationErrors["SenderEmail"] = "SenderEmail field cannot be blank";
        }

        if (empty($this->TextBody)) {
            $validationErrors["TextBody"] = "TextBody field cannot be blank";
        }

        return $validationErrors;
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord()) {
            $this->DateCreated = new RhubarbDateTime("now");
        }

        parent::beforeSave();
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        $simpleEmail = new SimpleEmail();

        $simpleEmail->setSubject($this->Subject);
        $simpleEmail->setText($this->TextBody);
        $simpleEmail->addRecipient($this->RecipientEmail, $this->RecipientName);
        $simpleEmail->setSender($this->SenderEmail, $this->SenderName);
        $simpleEmail->setHtml($this->HtmlBody);

        if (!empty($this->Attachments)) {
            foreach ($this->Attachments as $attachment) {
                $simpleEmail->addAttachment($attachment->path, $attachment->name);
            }
        }

        return $simpleEmail;
    }

    public function addAttachment($path, $newName = "")
    {
        $attachments = $this->Attachments;

        if ($newName == "") {
            $newName = basename($path);
        }

        $file = new \stdClass();
        $file->path = $path;
        $file->name = $newName;

        $attachments[] = $file;

        $this->Attachments = $attachments;
    }

    public static function FindUnsentCommunicationEmails($communicationID)
    {
        return self::Find(new AndGroup(
            [
                new Equals("CommunicationID", $communicationID),
                new Equals("Sent", false)
            ]
        ));
    }
}
