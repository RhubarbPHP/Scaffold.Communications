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
use Rhubarb\Stem\Schema\Columns\LongStringColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 * @property int $CommunicationID
 * @property string $Subject
 * @property string $Body
 * @property RhubarbDateTime $DateCreated
 * @property RhubarbDateTime $DateSent
 * @property RhubarbDateTime $DateToSend
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
            new DateTimeColumn("DateCreated"),
            new DateTimeColumn("DateSent"),
            new BooleanColumn("Sent", false)
        );

        return $schema;
    }

    public function setSent($newValue) {
        $this->setModelValue("Sent", $newValue);
        $this->setModelValue("DateSent", new RhubarbDateTime("now"));
    }

    public function setDateSent($newValue) {
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

        return $simpleEmail;
    }
}
