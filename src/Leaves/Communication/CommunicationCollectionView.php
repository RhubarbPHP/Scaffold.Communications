<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\Communication;

use Rhubarb\Leaf\Table\Leaves\Table;
use Rhubarb\Leaf\Views\View;

/**
 * @property CommunicationCollectionModel $model
 */
class CommunicationCollectionView extends View
{
    protected function createSubLeaves()
    {
        $this->registerSubLeaf(
            $table = new Table($this->model->restCollection->addSort('DateCreated', false))
        );

        $table->columns = [
            '#' => 'CommunicationID',
            'Title',
            'DateCreated',
            'DateToSend',
            'Status',
            'DateSent'
        ];
    }

    protected function printViewContent()
    {
        print $this->leaves['Table'];
    }
}
