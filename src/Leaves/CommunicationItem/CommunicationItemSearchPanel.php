<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Leaf\Controls\Common\DateTime\Date;
use Rhubarb\Leaf\Controls\Common\Text\TextBox;
use Rhubarb\Leaf\SearchPanel\Leaves\SearchPanel;
use Rhubarb\Pikaday\Pikaday;
use Rhubarb\Stem\Filters\Contains;
use Rhubarb\Stem\Filters\GreaterThan;
use Rhubarb\Stem\Filters\Group;
use Rhubarb\Stem\Filters\LessThan;

class CommunicationItemSearchPanel extends SearchPanel
{
    protected function createSearchControls()
    {
        if (class_exists(Pikaday::class)) {
            $dates = [
                new Pikaday('CreatedAfter'),
                new Pikaday('CreatedBefore'),
                new Pikaday('SentBefore'),
                new Pikaday('SentAfter')
            ];
        } else {
            /** @var Date[] $dates */
            $dates = [
                new Date('CreatedAfter'),
                new Date('CreatedBefore'),
                new Date('SentBefore'),
                new Date('SentAfter')
            ];

            foreach ($dates as $date) {
                $date->setOptional();
            }
        }

        return array_merge(
            [
                new TextBox('Subject'),
                new TextBox('Recipient')
            ],
            $dates
        );
    }

    public function populateFilterGroup(Group $filterGroup)
    {
        $searchValues = $this->model->searchValues;

        if ($searchValues['Subject']) {
            $filterGroup->addFilters(new Contains('Title', $searchValues['Subject']));
        }

        if ($searchValues['Recipient']) {
            $filterGroup->addFilters(new Contains('Recipient', $searchValues['Recipient']));
        }

        if ($searchValues['CreatedAfter'] instanceof RhubarbDateTime && $searchValues['CreatedAfter']->isValidDateTime()) {
            $filterGroup->addFilters(new GreaterThan('DateCreated', $searchValues['CreatedAfter']));
        }
        if ($searchValues['CreatedBefore'] instanceof RhubarbDateTime && $searchValues['CreatedBefore']->isValidDateTime()) {
            $filterGroup->addFilters(new LessThan('DateCreated', $searchValues['CreatedBefore']));
        }

        if ($searchValues['SentAfter'] instanceof RhubarbDateTime && $searchValues['SentAfter']->isValidDateTime()) {
            $filterGroup->addFilters(new GreaterThan('DateSent', $searchValues['SentAfter']));
        }
        if ($searchValues['SentBefore'] instanceof RhubarbDateTime && $searchValues['SentBefore']->isValidDateTime()) {
            $filterGroup->addFilters(new LessThan('DateSent', $searchValues['SentBefore']));
        }
    }
}
