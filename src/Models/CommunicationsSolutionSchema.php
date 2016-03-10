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
        parent::__construct(0.1);

        $this->addModel("Communication", __NAMESPACE__ . '\Communication', 0.1);
        $this->addModel("CommunicationItem", __NAMESPACE__ . '\CommunicationItem', 0.1);
    }

    protected function defineRelationships()
    {
        parent::defineRelationships();

        $this->declareOneToManyRelationships(
            [
                "Communication" =>
                [
                    "Items" => "CommunicationItem.CommunicationID"
                ]
            ]
        );
    }


}
