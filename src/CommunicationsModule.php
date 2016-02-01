<?php

namespace Rhubarb\Scaffolds\Communications;

use Rhubarb\Crown\Module;
use Rhubarb\Scaffolds\Communications\Custard\SendCommunicationsCommand;
use Rhubarb\Stem\Schema\SolutionSchema;

class CommunicationsModule extends Module
{
    protected function initialise()
    {
        SolutionSchema::registerSchema("CommunicationsSolutionSchema", __NAMESPACE__ . '\Models\CommunicationsSolutionSchema');
        parent::initialise();
    }

    public function getCustardCommands()
    {
        return [
            new SendCommunicationsCommand()
        ];
    }
}
