<?php

namespace Rhubarb\Scaffolds\Communications;

use Rhubarb\Crown\Module;
use Rhubarb\Scaffolds\Communications\Custard\SendCommunicationsCommand;
use Rhubarb\Scaffolds\Communications\Models\CommunicationsSolutionSchema;
use Rhubarb\Stem\Schema\SolutionSchema;

class CommunicationsModule extends Module
{
    protected function initialise()
    {
        SolutionSchema::registerSchema('CommunicationsSolutionSchema', CommunicationsSolutionSchema::class);
    }

    public function getCustardCommands()
    {
        return [
            new SendCommunicationsCommand()
        ];
    }
}
