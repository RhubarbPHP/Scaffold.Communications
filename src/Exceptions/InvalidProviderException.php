<?php

namespace Rhubarb\Scaffolds\Communications\Exceptions;

use Rhubarb\Crown\Exceptions\RhubarbException;

class InvalidProviderException extends RhubarbException
{
    public function __construct()
    {
        parent::__construct("Invalid Provider defined");
    }

}
