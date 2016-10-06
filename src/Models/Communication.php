<?php

namespace Rhubarb\Scaffolds\Communications\Models;

use Rhubarb\Crown\DateTime\RhubarbDate;
use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Sendables\Email\Email;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Filters\Not;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\MySqlEnumColumn;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\BooleanColumn;
use Rhubarb\Stem\Schema\Columns\DateTimeColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 * @property int $CommunicationID
 * @property \DateTime $DateCreated
 * @property string Title
 * @property \DateTime $DateSent
 * @property \DateTime $DateToSend
 * @property string $Status
 *
 * @property CommunicationItem[] $Items The items connected with the communication
 */
class Communication extends Model
{
    public function setDateSent($newValue)
    {
        throw new ModelConsistencyValidationException();
    }

    public function markSent()
    {
        $this->Status = "Sent";
        $this->modelData["DateSent"] = new RhubarbDate("now");
        $this->save();
    }

    protected function createSchema()
    {
        $schema = new ModelSchema("tblCommunication");

        $schema->addColumn(
            new AutoIncrementColumn("CommunicationID"),
            new StringColumn("Title", 150),
            new MySqlEnumColumn("Status", "Draft", ["Draft","Scheduled","Sent"]),
            new DateTimeColumn("DateCreated"),
            new DateTimeColumn("DateSent"),
            new DateTimeColumn("DateToSend")
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
        if ($this->Status != "Scheduled") {
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

    public static function findUnsentCommunications() {
        return self::find( new AndGroup(
            [
                new Equals("Status", "Scheduled")
            ]
        ));
    }
}
