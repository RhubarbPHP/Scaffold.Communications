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
 * @property \DateTime $DateToSend
 * @property bool $Completed
 *
 * @property CommunicationItem[] $Items The items connected with the communication
 */
class Communication extends Model
{
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
