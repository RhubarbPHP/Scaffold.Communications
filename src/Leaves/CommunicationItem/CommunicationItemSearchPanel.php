<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
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
        return [
            new TextBox('Title'),
            new TextBox('Recipient'),
            new Date('CreatedAfter'),
            new Date('CreatedBefore'),
            new Date('SentBefore'),
            new Date('SentAfter')
        ];
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
