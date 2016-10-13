<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\Communication;

use Rhubarb\Leaf\Crud\Leaves\ModelBoundLeaf;

/**
 * @property CommunicationCollectionModel $model
 */
class CommunicationCollection extends ModelBoundLeaf
{
    protected function getViewClass()
    {
        return CommunicationCollectionView::class;
    }

    protected function createModel()
    {
        return new CommunicationCollectionModel();
    }
}
