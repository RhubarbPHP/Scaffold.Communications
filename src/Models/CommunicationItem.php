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
 * @property int $CommunicationItemID
 * @property int $CommunicationID
 * @property string $Recipient
 * @property string $TextBody
 * @property RhubarbDateTime $DateCreated
 * @property RhubarbDateTime $DateSent
 * @property bool $Sent
 * @property mixed $Data
 */
class CommunicationItem extends Model
{
    protected function createSchema()
    {
        $schema = new ModelSchema("tblCommunicationItem");

        $schema->addColumn(
            new AutoIncrementColumn("CommunicationItemID"),
            new ForeignKeyColumn("CommunicationID"),
            new StringColumn("Type",50),
            new StringColumn("Recipient", 200),
            new LongStringColumn("TextBody"),
            new DateTimeColumn("DateCreated"),
            new DateTimeColumn("DateSent"),
            new BooleanColumn("Sent", false),
            new JsonColumn("Data", "", true)
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

        if (empty($this->Recipient)) {
            $validationErrors["Recipient"] = "Recipient field cannot be empty";
        }

        if (empty($this->TextBody)) {
            $validationErrors["TextBody"] = "TextBody field cannot be blank";
        }

        if (empty($this->Type)) {
            $validationErrors["Type"] = "Type field cannot be blank";
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
