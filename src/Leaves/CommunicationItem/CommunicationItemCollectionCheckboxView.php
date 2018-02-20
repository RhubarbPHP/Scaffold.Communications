<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

use Rhubarb\Leaf\Controls\Common\Checkbox\CheckboxView;

class CommunicationItemCollectionCheckboxView extends CheckboxView
{
    protected function printViewContent()
    {
        ?>
        <label class="switch">
            <?php
            $attributes = $this->getNameValueClassAndAttributeString(false);
            $attributes .= $this->model->value ? ' checked="checked"' : '';

            $presence = $this->getPresenceInputName();
            print "<input type='hidden' name='{$presence}' value='0'><input type='checkbox' {$attributes}/>";
            ?>
            <div class="slider"></div>
        </label>
        <?php
    }

    /**
     * @return string
     */
    protected function getPresenceInputName()
    {
        return "set_{$this->model->leafPath}_";
    }
}
