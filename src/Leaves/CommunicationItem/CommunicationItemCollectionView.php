<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

use Rhubarb\Leaf\Leaves\LeafDeploymentPackage;
use Rhubarb\Leaf\Table\Leaves\Columns\DateColumn;
use Rhubarb\Leaf\Table\Leaves\Table;
use Rhubarb\Leaf\Views\View;
use Rhubarb\Scaffolds\Communications\Decorators\CommunicationDecorator;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Filters\Not;

/**
 * @property CommunicationItemCollectionModel $model
 */
class CommunicationItemCollectionView extends View
{
    protected function createSubLeaves()
    {
        $communicationItems = $this->model->restCollection;
        if ($this->model->archive) {
            $communicationItems->filter(new Not(new Equals('Status', 'Not Sent')));
        }
        $communicationItems->intersectWith(Communication::find(), 'CommunicationID', 'CommunicationID', ['Title', 'DateToSend']);

        $this->registerSubLeaf(
            $table = new Table($communicationItems->addSort('DateCreated', false))
        );

        $table->columns = [
            '#' => 'CommunicationItemID',
            'Title' => '{Title}',
            'Recipient',
            'DateCreated',
            'Date to Send' => new DateColumn('DateToSend', 'Date to Send', CommunicationDecorator::DATE_FORMAT),
            'DateSent',
            'Status',
            '' => '<a class="view-content-button" data-id="{CommunicationItemID}" href="#ViewContent">View Content</a>'
        ];
    }

    protected function printCommunicationContentDialog()
    {
        ?>
        <div class="c-modal-container js-content-modal">
            <div class="c-modal c-modal--small">
                <div class="c-modal__header">
                    <span class="js-modal-close c-modal__close"><a href="#Close">Close</a></span>
                    <h2 class="o-heading u-gamma">Communication Content</h2>
                </div>
                <div class="c-modal__body">
                </div>
            </div>
        </div>
        <?php
    }

    protected function printViewContent()
    {
        $this->printCommunicationContentDialog();

        print $this->leaves['Table'];
    }

    protected function getViewBridgeName()
    {
        return 'CommunicationItemCollectionViewBridge';
    }

    public function getDeploymentPackage()
    {
        /** @var LeafDeploymentPackage $deploymentPackage */
        $deploymentPackage = parent::getDeploymentPackage();
        $deploymentPackage->resourcesToDeploy[] = __DIR__ . '/CommunicationItemCollectionViewBridge.js';

        return $deploymentPackage;
    }
}
