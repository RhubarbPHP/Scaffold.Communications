<?php

namespace Rhubarb\Scaffolds\Communications\Models;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
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
 * @property string $Type
 * @property string $Subject
 * @property string $Body
 * @property RhubarbDateTime $DateCreated
 * @property RhubarbDateTime $DateSent
 * @property RhubarbDateTime $DateToSend
 * @property bool $Sent
 */
class Communication extends Model
{
    const TYPE_BLANK = '';
    const TYPE_EMAIL = "Email";

    /**
     * Returns the schema for this data object.
     *
     * @return \Rhubarb\Stem\Schema\ModelSchema
     */
    protected function createSchema()
    {
        $schema = new ModelSchema("tblCommunication");

        $schema->addColumn(
            new AutoIncrementColumn("CommunicationID"),
            new MySqlEnumColumn("Type", self::TYPE_BLANK, [self::TYPE_BLANK, self::TYPE_EMAIL]),
            new CommaSeparatedListColumn("ToRecipients", 2000),
            new StringColumn("FromSender", 100),
            new StringColumn("Subject", 160),
            new LongStringColumn("Body"),
            new DateTimeColumn("DateCreated"),
            new DateTimeColumn("DateSent"),
            new DateTimeColumn("DateToSend"),
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

        if (empty($this->Type)) {
            $validationErrors["Type"] = "Type field cannot be blank";
        }

        if (empty($this->ToRecipients)) {
            $validationErrors["ToRecipients"] = "ToRecipients field cannot be empty";
        }

        if (empty($this->FromSender)) {
            $validationErrors["FromSender"] = "FromSender field cannot be blank";
        }

        if (empty($this->Body)) {
            $validationErrors["Body"] = "Body field cannot be blank";
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

    public function shouldSendCommunication()
    {
        $shouldSend = true;
        if ($this->DateToSend && $this->DateToSend->isValidDateTime()) {
            $currentDateTimestamp = strtotime(new RhubarbDateTime("now"));
            $dateToSendTimestamp = strtotime($this->DateToSend);

            $totalDifferenceInSeconds = $currentDateTimestamp - $dateToSendTimestamp;

            if ($totalDifferenceInSeconds < 0) {
                $shouldSend = false;
            }
        }

        return $shouldSend;
    }
}
