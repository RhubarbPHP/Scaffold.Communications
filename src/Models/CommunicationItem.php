<?php

namespace Rhubarb\Scaffolds\Communications\Models;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\MySqlEnumColumn;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\MySqlJsonColumn;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\BooleanColumn;
use Rhubarb\Stem\Schema\Columns\DateTimeColumn;
use Rhubarb\Stem\Schema\Columns\ForeignKeyColumn;
use Rhubarb\Stem\Schema\Columns\LongStringColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\Index;
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
 * @property string $FailureReason
 * @property bool $Sent
 * @property string $ProviderMessageID  An ID which identifies the message on the provider's system, e.g. Postmark's MessageID
 * @property string $ProviderStatus  The status of the message from the provider, e.g. from Postmark this might be "Opened", "Delivered", or "Bounced"
 * @property RhubarbDateTime $ProviderStatusChangeTime  The date and time of the last change to the ProviderStatus
 */
class CommunicationItem extends Model
{
    const STATUS_NOT_SENT = "Not Sent";
    const STATUS_SENT = "Sent";
    const STATUS_DELIVERED = "Delivered";
    const STATUS_OPENED = "Opened";
    const STATUS_FAILED = 'Failed';
    const STATUS_HARD_BOUNCE = "Hard bounce";
    const STATUS_SOFT_BOUNCE = "Soft bounce";

    protected function createSchema()
    {
        $schema = new ModelSchema("tblCommunicationItem");

        $schema->addColumn(
            new AutoIncrementColumn("CommunicationItemID"),
            new ForeignKeyColumn("CommunicationID"),
            new MySqlEnumColumn("Status", self::STATUS_NOT_SENT, [self::STATUS_NOT_SENT, self::STATUS_SENT, self::STATUS_DELIVERED, self::STATUS_OPENED, self::STATUS_FAILED, self::STATUS_HARD_BOUNCE, self::STATUS_SOFT_BOUNCE]),
            new StringColumn("Type", 50),
            new StringColumn("SendableClassName", 150),
            new StringColumn("Recipient", 200),
            new LongStringColumn("Text"),
            new MySqlJsonColumn("Data", "", true),
            new DateTimeColumn("DateCreated"),
            new DateTimeColumn("DateSent"),
            new StringColumn("FailureReason", 500),
            new BooleanColumn("Sent", false),
            new StringColumn("ProviderMessageID", 200),
            new StringColumn("ProviderStatus", 50),
            new DateTimeColumn("ProviderStatusChangeTime")
        );

        $schema->addIndex(new Index("ProviderMessageID"));

        return $schema;
    }

    public function markSent()
    {
        $this->Status = self::STATUS_SENT;
        $this->DateSent = new RhubarbDateTime("now");
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
            new Equals("Sent", false)
        ]));
    }
}
