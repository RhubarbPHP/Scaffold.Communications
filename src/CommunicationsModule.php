<?php

namespace Rhubarb\Scaffolds\Communications;

use Rhubarb\Crown\Module;
use Rhubarb\Crown\String\StringTools;
use Rhubarb\Leaf\Crud\UrlHandlers\CrudUrlHandler;
use Rhubarb\Scaffolds\Communications\Custard\SendCommunicationsCommand;
use Rhubarb\Scaffolds\Communications\Decorators\CommunicationDecorator;
use Rhubarb\Scaffolds\Communications\Leaves\Communication\CommunicationCollection;
use Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem\CommunicationItemArchiveCollection;
use Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem\CommunicationItemCollection;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Scaffolds\Communications\Models\CommunicationsSolutionSchema;
use Rhubarb\Stem\Decorators\DataDecorator;
use Rhubarb\Stem\Schema\SolutionSchema;

class CommunicationsModule extends Module
{
    protected function initialise()
    {
        SolutionSchema::registerSchema('CommunicationsSolutionSchema', CommunicationsSolutionSchema::class);

        DataDecorator::registerDecoratorClass(CommunicationDecorator::class, CommunicationItem::class);
        DataDecorator::registerDecoratorClass(CommunicationDecorator::class, Communication::class);
    }

    public function getCustardCommands()
    {
        return [
            new SendCommunicationsCommand()
        ];
    }

    protected function registerUrlHandlers()
    {
        $this->addUrlHandlers([
            "/communications/" => new CrudUrlHandler(Communication::class, StringTools::getNamespaceFromClass(CommunicationCollection::class)),
            "/communication-items/" => new CrudUrlHandler(
                CommunicationItem::class,
                StringTools::getNamespaceFromClass(CommunicationItemCollection::class),
                ['archive' => CommunicationItemArchiveCollection::class]
            )
        ]);
    }
}
