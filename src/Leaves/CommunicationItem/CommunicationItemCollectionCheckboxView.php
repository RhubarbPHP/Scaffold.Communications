<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

use Rhubarb\Leaf\Controls\Common\Checkbox\CheckboxView;

class CommunicationItemCollectionCheckboxView extends CheckboxView
{
    protected function printViewContent()
    {
        ?>
        <label class="switch">
            <?= parent::printViewContent();?>
            <div class="slider"></div>
        </label>
        <?php
    }
}
