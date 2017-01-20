<?php

namespace Rhubarb\Scaffolds\Communications\Decorators;

use Rhubarb\Stem\Decorators\CommonDataDecorator;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\DateColumn;

class CommunicationDecorator extends CommonDataDecorator
{
    const DATE_FORMAT = "j M Y, g:ia";

    protected function registerTypeDefinitions()
    {
        parent::registerTypeDefinitions();

        $this->addTypeFormatter(DateColumn::class, function (Model $model, \DateTime $value) {
            return $value->format(self::DATE_FORMAT);
        });
    }
}
