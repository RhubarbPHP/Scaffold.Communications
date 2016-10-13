<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

/**
 * @property CommunicationItemCollectionModel $model
 */
class CommunicationItemArchiveCollection extends CommunicationItemCollection
{
    protected function getViewClass()
    {
        return CommunicationItemCollectionView::class;
    }

    protected function createModel()
    {
        $model = new CommunicationItemCollectionModel();
        $model->archive = true;
        return $model;
    }
}
