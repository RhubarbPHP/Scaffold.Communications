<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Crud\Leaves\ModelBoundModel;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Stem\Collections\Collection;

/**
 * @property CommunicationItem[]|Collection $restCollection
 */
class CommunicationItemCollectionModel extends ModelBoundModel
{
    public $archive = false;

    public $getContentForCommunicationItemEvent;

    public function __construct()
    {
        parent::__construct();

        $this->getContentForCommunicationItemEvent = new Event();
    }
}
