<?php

namespace Rhubarb\Scaffolds\Communications\Models;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\MySqlEnumColumn;
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
 * @property string $Status
 * @property string $Type
 * @property string $SendableClassName
 * @property string $Recipient
 * @property string $Text
 * @property \stdClass $Data
 * @property RhubarbDateTime $DateCreated
 * @property RhubarbDateTime $DateSent
 * @property bool $Sent
 */
class CommunicationItem extends Model
{
    const STATUS_NOT_SENT = "Not Sent";
    const STATUS_SENT = "Sent";
    const STATUS_DELIVERED = "Delivered";
    const STATUS_OPENED = "Opened";

    protected function createSchema()
    {
        $schema = new ModelSchema("tblCommunicationItem");

        $schema->addColumn(
            new AutoIncrementColumn("CommunicationItemID"),
            new ForeignKeyColumn("CommunicationID"),
            new MySqlEnumColumn("Status", self::STATUS_NOT_SENT, [self::STATUS_NOT_SENT, self::STATUS_SENT, self::STATUS_DELIVERED, self::STATUS_OPENED]),
            new StringColumn("Type", 50),
            new StringColumn("SendableClassName", 150),
            new StringColumn("Recipient", 200),
            new LongStringColumn("Text"),
            new JsonColumn("Data", "", true),
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

        if (empty($this->Recipient)) {
            $validationErrors["Recipient"] = "Recipient field cannot be empty";
        }

        if (empty($this->Text)) {
            $validationErrors["Text"] = "Text field cannot be blank";
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
    public function getSendable()
    {
        $className = $this->SendableClassName;

        return $className::fromArray($this->Data);
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

    public static function findUnsentCommunicationEmails($communicationID)
    {
        return self::find(new AndGroup([
            new Equals("CommunicationID", $communicationID),
            new Equals(self::STATUS_SENT, false)
        ]));
    }
}
