<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

use Rhubarb\Leaf\Controls\Common\Checkbox\Checkbox;

class CommunicationItemCollectionCheckbox extends Checkbox
{
    protected function getViewClass()
    {
        return CommunicationItemCollectionCheckboxView::class;
    }
}
