<?php
// Here you can initialize variables that will be available to your tests
namespace Rhubarb\Scaffolds\Communications\Tests;

use Rhubarb\Crown\Module;
use Rhubarb\Scaffolds\Communications\CommunicationsModule;

Module::registerModule(new CommunicationsModule());
Module::initialiseModules();
