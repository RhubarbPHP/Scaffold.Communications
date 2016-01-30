<?php

namespace Rhubarb\Scaffolds\Communications\Models;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Email\Email;
use Rhubarb\Crown\Email\SimpleEmail;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\MySqlEnumColumn;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\BooleanColumn;
use Rhubarb\Stem\Schema\Columns\CommaSeparatedListColumn;
use Rhubarb\Stem\Schema\Columns\DateTimeColumn;
use Rhubarb\Stem\Schema\Columns\JsonColumn;
use Rhubarb\Stem\Schema\Columns\LongStringColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 * @property int $CommunicationEmailID
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

    public function setAttachments($attachments)
    {
        $this->setModelValue("Attachments", json_encode($attachments));
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

        $attachmentsArray = json_decode($this->Attachments);
        if (!empty($attachmentsArray)) {
            foreach ($attachmentsArray as $attachment) {
                $simpleEmail->addAttachment($attachment->path, $attachment->name);
            }
        }

        return $simpleEmail;
    }

    public function addAttachment($path, $newName = "")
    {
        $attachments = json_decode($this->Attachments, true);

        if ($newName == "") {
            $newName = basename($path);
        }

        $file = new \stdClass();
        $file->path = $path;
        $file->name = $newName;

        $attachments[] = $file;

        $this->Attachments = $attachments;
    }
}
