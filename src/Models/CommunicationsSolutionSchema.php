<?php

namespace Rhubarb\Scaffolds\Communications\Models;

use Rhubarb\Stem\Schema\SolutionSchema;

/**
 * Class CommunicationsSolutionSchema
 * @package Rhubarb\Scaffolds\Communications\Models
 */
class CommunicationsSolutionSchema extends SolutionSchema
{
    public function __construct()
    {
        parent::__construct();

        $this->addModel("Communication", Communication::class, 1);
        $this->addModel("CommunicationItem", CommunicationItem::class, 2);
        $this->addModel("CommunicationItemSendAttempt", CommunicationItemSendAttempt::class, 1);
    }

    protected function defineRelationships()
    {
        parent::defineRelationships();

        $this->declareOneToManyRelationships([
            "Communication" => [
                "Items" => "CommunicationItem.CommunicationID"
            ]
        ]);
    }
}
