<?php

namespace Rhubarb\Scaffolds\Communications\Models;

use Rhubarb\Crown\DateTime\RhubarbDate;
use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\MySqlEnumColumn;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\DateTimeColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 * @property int $CommunicationID
 * @property string $Title
 * @property string $Status
 * @property RhubarbDateTime $DateCreated
 * @property RhubarbDateTime $DateSent
 * @property RhubarbDateTime $DateToSend
 *
 * @property CommunicationItem[]|Collection $Items The items connected with the communication
 */
class Communication extends Model
{
    const STATUS_DRAFT = "Draft";
    const STATUS_SCHEDULED = "Scheduled";
    const STATUS_SENT = "Sent";
    const STATUS_FAILED = 'Failed';

    public function setDateSent($newValue)
    {
        throw new ModelConsistencyValidationException();
    }

    public function markSent()
    {
        $this->Status = self::STATUS_SENT;
        $this->modelData["DateSent"] = new RhubarbDateTime("now");
        $this->save();
    }

    protected function createSchema()
    {
        $schema = new ModelSchema("tblCommunication");

        $schema->addColumn(
            new AutoIncrementColumn("CommunicationID"),
            new StringColumn("Title", 150),
            new MySqlEnumColumn("Status", self::STATUS_DRAFT, [self::STATUS_DRAFT, self::STATUS_SCHEDULED, self::STATUS_SENT, self::STATUS_FAILED]),
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
        if ($this->Status != self::STATUS_SCHEDULED) {
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
        if ($this->isNewRecord()) {
            $this->DateCreated = "now";
        }

        parent::beforeSave();
    }

    public static function findUnsentCommunications()
    {
        return self::find(new AndGroup([
            new Equals("Status", self::STATUS_SCHEDULED)
        ]));
    }
}
