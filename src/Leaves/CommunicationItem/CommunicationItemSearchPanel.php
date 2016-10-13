<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

use Rhubarb\Leaf\Controls\Common\DateTime\Date;
use Rhubarb\Leaf\Controls\Common\Text\TextBox;
use Rhubarb\Leaf\SearchPanel\Leaves\SearchPanel;
use Rhubarb\Stem\Filters\Contains;
use Rhubarb\Stem\Filters\GreaterThan;
use Rhubarb\Stem\Filters\Group;
use Rhubarb\Stem\Filters\LessThan;

class CommunicationItemSearchPanel extends SearchPanel
{
    protected function createSearchControls()
    {
        /** @var Date[] $dates */
        $dates[] = new Date('CreatedAfter');
        $dates[] = new Date('CreatedBefore');
        $dates[] = new Date('SentBefore');
        $dates[] = new Date('SentAfter');

        foreach ($dates as $date) {
            $date->setOptional();
        }

        return array_merge(
            [
                new TextBox('Title'),
                new TextBox('Recipient')
            ],
            $dates
        );
    }

    public function populateFilterGroup(Group $filterGroup)
    {
        $searchValues = $this->model->searchValues;

        if ($searchValues['Title']) {
            $filterGroup->addFilters(new Contains('Title', $searchValues['Title']));
        }

        if ($searchValues['Recipient']) {
            $filterGroup->addFilters(new Contains('Title', $searchValues['Recipient']));
        }

        if ($searchValues['CreatedAfter']) {
            $filterGroup->addFilters(new GreaterThan('DateCreated', $searchValues['CreatedAfter']));
        }
        if ($searchValues['CreatedBefore']) {
            $filterGroup->addFilters(new LessThan('DateCreated', $searchValues['CreatedBefore']));
        }

        if ($searchValues['SentAfter']) {
            $filterGroup->addFilters(new GreaterThan('DateSent', $searchValues['SentAfter']));
        }
        if ($searchValues['SentBefore']) {
            $filterGroup->addFilters(new LessThan('DateSent', $searchValues['SentBefore']));
        }
    }
}