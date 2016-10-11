<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

use Rhubarb\Leaf\Crud\Leaves\ModelBoundLeaf;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;

/**
 * @property CommunicationItemCollectionModel $model
 */
class CommunicationItemCollection extends ModelBoundLeaf
{
    protected function getViewClass()
    {
        return CommunicationItemCollectionView::class;
    }

    protected function createModel()
    {
        return new CommunicationItemCollectionModel();
    }

    protected function onModelCreated()
    {
        parent::onModelCreated();

        $this->model->getContentForCommunicationItemEvent->attachHandler(function ($communicationItemId) {
            if (empty((int)$communicationItemId)) {
                throw new \InvalidArgumentException('CommunicationItemID is required');
            }

            $communication = new CommunicationItem($communicationItemId);

            return $communication->Text;
        });
    }
}
