<?php

namespace Rhubarb\Scaffolds\Communications\Models;

use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\MySqlEnumColumn;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\DateTimeColumn;
use Rhubarb\Stem\Schema\Columns\ForeignKeyColumn;
use Rhubarb\Stem\Schema\Columns\IntegerColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

class CommunicationItemSendAttempt extends Model
{
    const STATUS_NOT_SENT = "Not Sent";
    const STATUS_SUCCESS = "Success";
    const STATUS_FAILED = "Failed";

    protected function createSchema()
    {
        $schema = new ModelSchema("tblCommunicationItemSendAttempt");

        $schema->addColumn(
            new AutoIncrementColumn("CommunicationItemSendAttemptID"),
            new ForeignKeyColumn("CommunicationItemID"),
            new DateTimeColumn("DateSent"),
            new IntegerColumn("SystemProcessID"),
            new MySqlEnumColumn("Status", self::STATUS_NOT_SENT, [self::STATUS_NOT_SENT, self::STATUS_SUCCESS, self::STATUS_FAILED]),
            new StringColumn("FailureReason", 500),
            new StringColumn("ProviderMessageID", 200)
        );

        return $schema;
    }

    protected function beforeSave()
    {
        parent::beforeSave();

        if ($this->isNewRecord()){
            $this->SystemProcessID = getmypid();
        }
    }
}